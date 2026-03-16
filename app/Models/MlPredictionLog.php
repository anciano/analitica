<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlPredictionLog extends Model
{
    protected $fillable = [
        'ml_model_id',
        'ml_model_version_id',
        'input_data',
        'output_data',
        'response_time_ms',
        'user_id'
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(MlModelVersion::class, 'ml_model_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
