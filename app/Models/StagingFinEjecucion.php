<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StagingFinEjecucion extends Model
{
    protected $table = 'stg_fin_ejecucion';

    protected $fillable = [
        'import_run_id',
        'row_number',
        'payload_raw',
        'payload_parsed',
        'is_valid',
        'validation_errors',
    ];

    protected $casts = [
        'payload_raw' => 'array',
        'payload_parsed' => 'array',
        'errors' => 'array',
        'is_valid' => 'boolean',
    ];
}
