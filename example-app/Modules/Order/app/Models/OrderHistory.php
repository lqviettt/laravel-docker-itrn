<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Order\Database\Factories\OrderHistoryFactory;

class OrderHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'status',
        'description',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
