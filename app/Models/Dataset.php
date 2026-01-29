<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dataset extends Model
{
    protected $fillable = ['slug', 'name'];

    public function versions(): HasMany
    {
        return $this->hasMany(DatasetVersion::class);
    }
}
