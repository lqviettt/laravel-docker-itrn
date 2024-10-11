<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['customer_name', 'customer_phone', 'status', 'shipping_address'];

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
