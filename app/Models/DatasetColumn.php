<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatasetColumn extends Model
{
    protected $fillable = ['dataset_version_id', 'canonical_name', 'data_type', 'is_required'];

    public function version(): BelongsTo
    {
        return $this->belongsTo(DatasetVersion::class, 'dataset_version_id');
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(DatasetColumnAlias::class);
    }
}
