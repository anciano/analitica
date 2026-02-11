<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinClasificadorItem;
use App\Models\FinCentroCosto;

class BudgetCatalogsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Centros de Costo
        $direccion = FinCentroCosto::create(['codigo' => '100', 'nombre' => 'Dirección']);
        $subadm = FinCentroCosto::create(['codigo' => '110', 'nombre' => 'Subdirección Administrativa', 'parent_id' => $direccion->id]);
        $submed = FinCentroCosto::create(['codigo' => '120', 'nombre' => 'Subdirección Médica', 'parent_id' => $direccion->id]);

        FinCentroCosto::create(['codigo' => '1101', 'nombre' => 'Recursos Humanos', 'parent_id' => $subadm->id]);
        FinCentroCosto::create(['codigo' => '1102', 'nombre' => 'Finanzas', 'parent_id' => $subadm->id]);
        FinCentroCosto::create(['codigo' => '1201', 'nombre' => 'Pabellón', 'parent_id' => $submed->id]);
        FinCentroCosto::create(['codigo' => '1202', 'nombre' => 'Urgencias', 'parent_id' => $submed->id]);

        // 2. Clasificador Presupuestario (Ejemplo Subtítulo 21 - Personal)
        $sub21 = FinClasificadorItem::create([
            'codigo' => '21',
            'denominacion' => 'GASTOS EN PERSONAL',
            'nivel' => 1,
            'anio_vigencia' => 2026
        ]);

        $item2101 = FinClasificadorItem::create([
            'codigo' => '21.01',
            'denominacion' => 'Personal de Planta',
            'nivel' => 2,
            'parent_id' => $sub21->id,
            'anio_vigencia' => 2026
        ]);

        $item2102 = FinClasificadorItem::create([
            'codigo' => '21.02',
            'denominacion' => 'Personal a Contrata',
            'nivel' => 2,
            'parent_id' => $sub21->id,
            'anio_vigencia' => 2026
        ]);

        // Subtítulo 22 - Bienes y Servicios
        $sub22 = FinClasificadorItem::create([
            'codigo' => '22',
            'denominacion' => 'BIENES Y SERVICIOS DE CONSUMO',
            'nivel' => 1,
            'anio_vigencia' => 2026
        ]);

        FinClasificadorItem::create([
            'codigo' => '22.01',
            'denominacion' => 'Alimentos y Bebidas',
            'nivel' => 2,
            'parent_id' => $sub22->id,
            'anio_vigencia' => 2026
        ]);
    }
}
