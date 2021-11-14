<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Increases the inventory when any detail is deleted or updated
     * Decreases the total inventory of the product when an order placed
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($detail) { // before delete() method call this
            Product::find($detail->product_id)->increment('stock', (int)$detail->quantity);
        });

        static::creating(function ($detail) { // before create() method call this
            Product::find($detail->product_id)->decrement('stock', (int)$detail->quantity);
        });
    }

}
