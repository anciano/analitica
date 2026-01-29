<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportError extends Model
{
    protected $fillable = [
        'import_run_id',
        'row_number',
        'column_name',
        'error_code',
        'error_message',
        'original_value',
    ];

    public function importRun(): BelongsTo
    {
        return $this->belongsTo(ImportRun::class);
    }
}
