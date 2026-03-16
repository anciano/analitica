<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlModelVersion extends Model
{
    protected $fillable = [
        'ml_model_id', 
        'version_tag', 
        'algorithm', 
        'dataset_info', 
        'features', 
        'metrics', 
        'artifact_path', 
        'status'
    ];

    protected $casts = [
        'dataset_info' => 'array',
        'features' => 'array',
        'metrics' => 'array',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'ml_model_id');
    }
}
