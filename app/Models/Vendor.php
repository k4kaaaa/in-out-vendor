<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = ['name', 'address', 'contact'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
