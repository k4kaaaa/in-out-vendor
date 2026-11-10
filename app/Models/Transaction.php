<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'date', 'type', 'product_id', 'vendor_id', 'qty', 
        'customer_name', 'receiver_name', 'photo_product', 'photo_delivery'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function stockMovement()
    {
        return $this->hasOne(StockMovement::class);
    }
}
