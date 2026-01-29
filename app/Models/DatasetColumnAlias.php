<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetColumnAlias extends Model
{
    protected $fillable = ['dataset_column_id', 'alias'];

    public function column(): BelongsTo
    {
        return $this->belongsTo(DatasetColumn::class, 'dataset_column_id');
    }
}
