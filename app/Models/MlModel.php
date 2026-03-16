<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlModel extends Model
{
    protected $fillable = ['name', 'code', 'description', 'active_version_id'];

    public function versions(): HasMany
    {
        return $this->hasMany(MlModelVersion::class);
    }

    public function activeVersion(): BelongsTo
    {
        return $this->belongsTo(MlModelVersion::class, 'active_version_id');
    }
}
