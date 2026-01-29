<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatasetVersion extends Model
{
    protected $fillable = ['dataset_id', 'version', 'is_active'];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(DatasetColumn::class);
    }
}
