<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinCentroCosto extends Model
{
    protected $table = 'fin_centros_costo';

    protected $fillable = [
        'codigo',
        'nombre',
        'nivel',
        'parent_id',
        'activo',
    ];

    protected static function booted()
    {
        static::creating(function ($cc) {
            $cc->calculateHierarchy();
        });

        static::updating(function ($cc) {
            if ($cc->isDirty('codigo')) {
                $cc->calculateHierarchy();
            }
        });
    }

    public function calculateHierarchy()
    {
        // Limpiar código de puntos o espacios
        $this->codigo = preg_replace('/[^0-9]/', '', $this->codigo);
        $len = strlen($this->codigo);

        if ($len <= 1) {
            $this->nivel = 1;
        } elseif ($len <= 3) {
            $this->nivel = 2;
        } elseif ($len <= 4) {
            $this->nivel = 3;
        } elseif ($len <= 6) {
            $this->nivel = 4;
        } else {
            $this->nivel = 5;
        }

        $parentCode = null;
        if ($this->nivel == 2) {
            $parentCode = substr($this->codigo, 0, 1);
        } elseif ($this->nivel == 3) {
            $parentCode = substr($this->codigo, 0, 3);
        } elseif ($this->nivel == 4) {
            $parentCode = substr($this->codigo, 0, 4);
        } elseif ($this->nivel == 5) {
            $parentCode = substr($this->codigo, 0, 6);
        }

        if ($parentCode) {
            $parent = self::where('codigo', $parentCode)->first();
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
        return $this->hasMany(FinPlanItem::class, 'centro_costo_id');
    }
}
