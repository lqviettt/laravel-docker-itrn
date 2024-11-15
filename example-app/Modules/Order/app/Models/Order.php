<?php

namespace Modules\Order\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Models\Product;

class Order extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['code', 'created_by', 'firstname', 'lastname', 'customer_phone', 'customer_email', 'status', 'shipping_address'];

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
            ->withPivot('quantity', 'price');
    }

    public function logs()
    {
        return $this->hasMany(OrderHistory::class);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            $order->created_by = Auth::user()->user_name;
        });

        static::updating(function ($order) {
            $order->created_by = Auth::user()->user_name;

            if ($order->status === 'canceled') {
                $order->logs()->create([
                    'status' => 'canceled',
                    'description' => 'Order has been canceled, stock returned.',
                ]);
            }
        });
    }

    public function scopeSearchByNameCode($query, $search)
    {
        return $query->when(
            !is_null($search),
            fn($query) => $query->where(function ($query) use ($search) {
                $query->where('lastname', 'like', $search . '%')
                    ->orWhere('firstname', 'like', $search . '%')
                    ->orWhere('code', 'like', $search . '%');
            })
        );
    }

    public function scopeSearchByPhone($query, $phone)
    {
        return $query->when(
            !is_null($phone),
            fn($query) => $query->where(function ($query) use ($phone) {
                $query->where('customer_phone', 'like', '%' . $phone . '%');
            })
        );
    }
}
