<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ ADD THIS

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Item belongs to cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Item belongs to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
