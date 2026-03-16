<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrdEgresoFact extends Model
{
    protected $table = 'grd_egresos_fact';

    protected $fillable = [
        'import_run_id',
        'anio',
        'mes',
        'mes_nombre',
        'num_historia',
        'episodio_cmbd',
        'prevision',
        'sexo',
        'grd_id_original',
        'grd_nombre',
        'dx_principal',
        'dx_secundarios',
        'proc_principal',
        'proc_secundarios',
        'estancia_media',
        'corte_superior',
        'tiene_vm',
        'peso_grd',
        'egresos',
        'edad',
    ];

    protected $casts = [
        'tiene_vm' => 'boolean',
        'estancia_media' => 'float',
        'corte_superior' => 'float',
        'peso_grd' => 'float',
        'egresos' => 'integer',
        'anio' => 'integer',
        'mes' => 'integer',
        'edad' => 'integer',
        'dx_secundarios' => 'array',
        'proc_secundarios' => 'array',
    ];

    public function importRun(): BelongsTo
    {
        return $this->belongsTo(ImportRun::class, 'import_run_id');
    }
}
