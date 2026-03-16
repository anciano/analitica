<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StagingGrdEgreso extends Model
{
    protected $table = 'stg_grd_egresos';

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
        'validation_errors' => 'array',
        'is_valid' => 'boolean',
    ];

    public function importRun(): BelongsTo
    {
        return $this->belongsTo(ImportRun::class, 'import_run_id');
    }
}
