<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinPlanMensual extends Model
{
    protected $table = 'fin_plan_mensual';

    protected $fillable = [
        'plan_item_id',
        'mes',
        'monto_planificado',
    ];

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(FinPlanItem::class, 'plan_item_id');
    }
}
