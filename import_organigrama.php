<?php

use App\Models\FinCentroCosto;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    ['codigo' => '100', 'nombre' => 'Dirección', 'parent' => null],
    ['codigo' => '110', 'nombre' => 'Subdirección Administrativa', 'parent' => '100'],
    ['codigo' => '120', 'nombre' => 'Subdirección Médica', 'parent' => '100'],
    ['codigo' => '130', 'nombre' => 'Subdirección de Gestión del Cuidado', 'parent' => '100'],
    ['codigo' => '140', 'nombre' => 'Subdirección de Gestión de las Personas', 'parent' => '100'],

    // Unidades de Dirección
    ['codigo' => '1001', 'nombre' => 'Unidad de Análisis', 'parent' => '100'],
    ['codigo' => '1002', 'nombre' => 'Asesor Clínico', 'parent' => '100'],

    // Médica
    ['codigo' => '1201', 'nombre' => 'Pabellón', 'parent' => '120'],
    ['codigo' => '1202', 'nombre' => 'Urgencia Adulto', 'parent' => '120'],
    ['codigo' => '1203', 'nombre' => 'Urgencia Pediátrica', 'parent' => '120'],
    ['codigo' => '1204', 'nombre' => 'Pediatría', 'parent' => '120'],
    ['codigo' => '1205', 'nombre' => 'Cirugía', 'parent' => '120'],
    ['codigo' => '1206', 'nombre' => 'Hospitalización Domiciliaria', 'parent' => '120'],
    ['codigo' => '1207', 'nombre' => 'UTI Adulto', 'parent' => '120'],
    ['codigo' => '1208', 'nombre' => 'UCI Adulto', 'parent' => '120'],
    ['codigo' => '1209', 'nombre' => 'Unidad Paciente Crítico Pediátrico', 'parent' => '120'],
    ['codigo' => '1210', 'nombre' => 'Pensionado / Transición', 'parent' => '120'],

    // Cuidado
    ['codigo' => '1301', 'nombre' => 'Esterilización', 'parent' => '130'],

    // Personas
    ['codigo' => '1401', 'nombre' => 'Oficina de Personal', 'parent' => '140'],
    ['codigo' => '1402', 'nombre' => 'Salud Ocupacional', 'parent' => '140'],
    ['codigo' => '1403', 'nombre' => 'Oficina de Capacitación', 'parent' => '140'],
    ['codigo' => '1404', 'nombre' => 'Sala Cuna', 'parent' => '140'],

    // Administrativa -> Abastecimiento
    ['codigo' => '1101', 'nombre' => 'Subdepto. Abastecimiento', 'parent' => '110'],
    ['codigo' => '110101', 'nombre' => 'Unidad Bodega de Insumos', 'parent' => '1101'],
    ['codigo' => '110102', 'nombre' => 'Unidad de Compras de Bienes y Servicios', 'parent' => '1101'],
    ['codigo' => '110103', 'nombre' => 'Unidad de Compras de Servicios Personales y Profesionales', 'parent' => '1101'],
    ['codigo' => '110104', 'nombre' => 'Unidad de Gestión y Administración de Contratos', 'parent' => '1101'],
    ['codigo' => '110105', 'nombre' => 'Unidad Bodega de Fármacos', 'parent' => '1101'],

    // Administrativa -> Finanzas
    ['codigo' => '1102', 'nombre' => 'Subdepto. Gestión de Finanzas e Ingresos', 'parent' => '110'],
    ['codigo' => '110201', 'nombre' => 'Unidad Gestión de Finanzas', 'parent' => '1102'],
    ['codigo' => '110202', 'nombre' => 'Unidad Gestión de Ingresos', 'parent' => '1102'],

    // Administrativa -> Operaciones
    ['codigo' => '1103', 'nombre' => 'Subdepto. de Operaciones', 'parent' => '110'],
    ['codigo' => '110301', 'nombre' => 'Unidad de Equipos Médicos', 'parent' => '1103'],
    ['codigo' => '110302', 'nombre' => 'Unidad de Infraestructura', 'parent' => '1103'],
    ['codigo' => '110303', 'nombre' => 'Unidad de Equipos Industriales', 'parent' => '1103'],
    ['codigo' => '110304', 'nombre' => 'Unidad de Servicios Generales', 'parent' => '1103'],

    // Administrativa -> Innovación
    ['codigo' => '1104', 'nombre' => 'Subdepto. de Innovación y Desarrollo', 'parent' => '110'],
    ['codigo' => '110401', 'nombre' => 'Unidad de Soporte e Infraestructura', 'parent' => '1104'],
    ['codigo' => '110402', 'nombre' => 'Unidad de Desarrollo y Gestión de Sistemas de Información', 'parent' => '1104'],
];

foreach ($data as $row) {
    $parent = null;
    if ($row['parent']) {
        $parent = FinCentroCosto::where('codigo', $row['parent'])->first();
    }

    FinCentroCosto::updateOrCreate(
        ['codigo' => $row['codigo']],
        ['nombre' => $row['nombre'], 'parent_id' => $parent ? $parent->id : null, 'activo' => true]
    );
}

echo "Organigrama importado exitosamente.\n";
