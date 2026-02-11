<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinPlanItem extends Model
{
    protected $table = 'fin_plan_items';

    protected $fillable = [
        'plan_id',
        'clasificador_item_id',
        'centro_costo_id',
        'monto_anual',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(FinPlan::class, 'plan_id');
    }

    public function clasificadorItem(): BelongsTo
    {
        return $this->belongsTo(FinClasificadorItem::class, 'clasificador_item_id');
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(FinCentroCosto::class, 'centro_costo_id');
    }

    public function mensualizaciones(): HasMany
    {
        return $this->hasMany(FinPlanMensual::class, 'plan_item_id');
    }
}
