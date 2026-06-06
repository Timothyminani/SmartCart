<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $fillable = [
        'user_id',
        'phone',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'address',
        'delivery_method'
    ];








    public function order()
{
    return $this->belongsTo(Order::class);
}


}
