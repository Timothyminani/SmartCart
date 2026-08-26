<?php

namespace App\Models;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{


protected $fillable = [
    'name',
    'slug',
    'description',
    'price',
    'sale_price',
    'stock_quantity',
    'category_id',
    'brand_id',
    'is_active',
    'is_featured',
];





    public function category()
{
    return $this->belongsTo(Category::class);
}

public function brand()
{
    return $this->belongsTo(Brand::class);
}

public function attributes()
{
    return $this->hasMany(ProductAttribute::class);
}

public function images()
{
    return $this->hasMany(ProductImage::class);
}

public function cartItems()
{
    return $this->hasMany(CartItem::class);
}

public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

}
