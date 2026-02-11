<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinClasificadorItem extends Model
{
    protected $table = 'fin_clasificador_items';

    protected $fillable = [
        'codigo',
        'denominacion',
        'nivel',
        'parent_id',
        'anio_vigencia',
        'activo',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->calculateHierarchy();
        });

        static::updating(function ($item) {
            if ($item->isDirty('codigo')) {
                $item->calculateHierarchy();
            }
        });
    }

    public function calculateHierarchy()
    {
        $this->codigo = preg_replace('/[^0-9]/', '', $this->codigo);
        $len = strlen($this->codigo);

        if ($len <= 2)
            $this->nivel = 1;
        elseif ($len <= 4)
            $this->nivel = 2;
        elseif ($len <= 7)
            $this->nivel = 3;
        elseif ($len <= 10)
            $this->nivel = 4;
        else
            $this->nivel = 5;

        $parentCode = null;
        if ($this->nivel == 2)
            $parentCode = substr($this->codigo, 0, 2);
        elseif ($this->nivel == 3)
            $parentCode = substr($this->codigo, 0, 4);
        elseif ($this->nivel == 4)
            $parentCode = substr($this->codigo, 0, 7);
        elseif ($this->nivel == 5)
            $parentCode = substr($this->codigo, 0, 10);

        if ($parentCode) {
            $parent = self::where('codigo', $parentCode)
                ->where('anio_vigencia', $this->anio_vigencia ?? 2026)
                ->first();
            $this->parent_id = $parent ? $parent->id : null;
        } else {
            $this->parent_id = null;
        }
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function planItems(): HasMany
    {
        return $this->hasMany(FinPlanItem::class, 'clasificador_item_id');
    }
}
