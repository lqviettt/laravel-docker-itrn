<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Models\Product;

// use Modules\Order\Database\Factories\OrderItemFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
