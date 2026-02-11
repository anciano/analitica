<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinPlan extends Model
{
    protected $table = 'fin_planes';

    protected $fillable = [
        'anio',
        'version',
        'nombre',
        'estado',
        'aprobado_at',
        'aprobado_by',
    ];

    protected $casts = [
        'aprobado_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FinPlanItem::class, 'plan_id');
    }

    public function aprobadoBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_by');
    }
}
