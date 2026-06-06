<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ ADD THIS


class Cart extends Model
{
     use HasFactory;

    protected $fillable = ['user_id'];

    // A cart has many items
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Cart belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
