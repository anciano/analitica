<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\DatasetVersion;
use App\Models\DatasetColumn;
use App\Models\DatasetColumnAlias;
use Illuminate\Database\Seeder;

class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        $dataset = Dataset::updateOrCreate([
            'slug' => 'fin_ejecucion'
        ], [
            'name' => 'Ejecución Presupuestaria de Gastos',
        ]);

        $version = $dataset->versions()->create([
            'version' => 'v1',
            'is_active' => true,
        ]);

        $columns = [
            ['name' => 'subtitulo', 'type' => 'string', 'required' => true, 'aliases' => ['Subtítulo', 'Subt.']],
            ['name' => 'item', 'type' => 'string', 'required' => true, 'aliases' => ['Item', 'Ítem']],
            ['name' => 'asignacion', 'type' => 'string', 'required' => false, 'aliases' => ['Asignación', 'Asig.']],
            ['name' => 'concepto', 'type' => 'string', 'required' => false, 'aliases' => ['Denominación', 'Concepto']],
            ['name' => 'presupuesto_vigente', 'type' => 'numeric', 'required' => false, 'aliases' => ['Presup. Vigente', 'Vigente']],
            ['name' => 'compromiso', 'type' => 'numeric', 'required' => false, 'aliases' => ['Compromiso']],
            ['name' => 'devengado', 'type' => 'numeric', 'required' => true, 'aliases' => ['Devengado', 'Monto Devengado']],
            ['name' => 'pagado', 'type' => 'numeric', 'required' => false, 'aliases' => ['Pagado']],
            ['name' => 'saldo', 'type' => 'numeric', 'required' => false, 'aliases' => ['Saldo']],
        ];

        foreach ($columns as $colData) {
            $column = $version->columns()->create([
                'canonical_name' => $colData['name'],
                'data_type' => $colData['type'],
                'is_required' => $colData['required'],
            ]);

            foreach ($colData['aliases'] as $alias) {
                $column->aliases()->create(['alias' => $alias]);
            }
        }

        // --- DATASET HR: Dotación ---
        $hrDataset = Dataset::updateOrCreate([
            'slug' => 'hr_dotacion'
        ], [
            'name' => 'Dotación de Personal (RRHH)',
        ]);

        $hrVersion = $hrDataset->versions()->create([
            'version' => 'v1',
            'is_active' => true,
        ]);

        $hrColumns = [
            ['name' => 'rut', 'type' => 'string', 'required' => true, 'aliases' => ['Rut', 'RUT']],
            ['name' => 'nombre_unidad', 'type' => 'string', 'required' => false, 'aliases' => ['Unidad', 'Centro de Costo']],
            ['name' => 'estamento', 'type' => 'string', 'required' => true, 'aliases' => ['Estamento', 'Agrupación']],
            ['name' => 'calidad_juridica', 'type' => 'string', 'required' => true, 'aliases' => ['Calidad Jurídica', 'Ley']],
            ['name' => 'horas', 'type' => 'numeric', 'required' => true, 'aliases' => ['Horas', 'Hrs']],
            ['name' => 'total_haberes', 'type' => 'numeric', 'required' => false, 'aliases' => ['Remuneración', 'Sueldo']],
        ];

        foreach ($hrColumns as $colData) {
            $column = $hrVersion->columns()->create([
                'canonical_name' => $colData['name'],
                'data_type' => $colData['type'],
                'is_required' => $colData['required'],
            ]);

            foreach ($colData['aliases'] as $alias) {
                $column->aliases()->create(['alias' => $alias]);
            }
        }
    }
}
