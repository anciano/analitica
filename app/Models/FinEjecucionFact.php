<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinEjecucionFact extends Model
{
    protected $table = 'fin_ejecucion_fact';

    protected $fillable = [
        'anio',
        'mes',
        'subtitulo',
        'item',
        'asignacion',
        'concepto',
        'presupuesto_vigente',
        'compromiso',
        'devengado',
        'pagado',
        'saldo',
        'fuente',
        'import_run_id',
        'nivel',
        'codigo_completo',
        'requerimiento',
        'deuda_flotante',
        'saldo_por_aplicar',
        'saldo_por_devengar',
        'row_number',
        'centro_costo_id',
    ];

    public function centroCosto()
    {
        return $this->belongsTo(FinCentroCosto::class, 'centro_costo_id');
    }
}
