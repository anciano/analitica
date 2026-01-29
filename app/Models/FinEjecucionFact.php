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
    ];
}
