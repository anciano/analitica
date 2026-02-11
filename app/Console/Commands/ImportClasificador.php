<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\FinClasificadorItem;
use Illuminate\Support\Facades\DB;

class ImportClasificador extends Command
{
    protected $signature = 'import:clasificador {file}';
    protected $description = 'Importa el clasificador presupuestario desde un archivo Excel';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Archivo no encontrado: $filePath");
            return 1;
        }

        $this->info("Cargando archivo: $filePath");
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Saltamos la cabecera
        array_shift($rows);

        $this->info("Procesando " . count($rows) . " filas...");

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (empty($row[0]))
                    continue;

                $codigoInput = trim((string) $row[0]);
                $denominacion = trim((string) $row[1]);

                // Limpiamos espacios no visibles
                $codigoInput = preg_replace('/\s+/', '', $codigoInput);

                // Determinamos nivel y jerarquía basados en la longitud o estructura
                // Asumimos formato chileno estándar si es posible o simplemente jerarquía por prefijo

                $this->upsertItem($codigoInput, $denominacion);
            }
            DB::commit();
            $this->info("Importación completada con éxito.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error durante la importación: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function upsertItem($codigo, $denominacion)
    {
        // El año de vigencia lo fijamos en 2026 según el contexto del usuario
        $anio = 2026;

        // Intentamos inferir la jerarquía
        // E.g. 2101001 -> 21 (Nivel 1), 2101 (Nivel 2), 2101001 (Nivel 3)

        $len = strlen($codigo);
        $nivel = 1;
        $parentId = null;

        if ($len > 2) {
            // Buscamos el padre (los primeros N-X caracteres)
            // Esto es simplificado, en SIGFE suele ser 2.2.3.3
            if ($len <= 4)
                $parentCodigo = substr($codigo, 0, 2);
            elseif ($len <= 7)
                $parentCodigo = substr($codigo, 0, 4);
            else
                $parentCodigo = substr($codigo, 0, 7);

            $parent = FinClasificadorItem::where('codigo', $parentCodigo)
                ->where('anio_vigencia', $anio)
                ->first();

            if ($parent) {
                $parentId = $parent->id;
                $nivel = $parent->nivel + 1;
            }
        }

        FinClasificadorItem::updateOrCreate(
            ['codigo' => $codigo, 'anio_vigencia' => $anio],
            ['denominacion' => $denominacion, 'nivel' => $nivel, 'parent_id' => $parentId, 'activo' => true]
        );
    }
}
