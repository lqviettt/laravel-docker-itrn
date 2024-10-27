<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['code', 'created_by', 'firstname', 'lastname', 'customer_phone', 'status', 'shipping_address'];

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
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCreatedBy($query, $created_by)
    {
        return $query->where('created_by', $created_by);
    }

    public function scopeSearchNameCodePhone($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('lastname', 'like',  $search . '%')
                ->orWhere('firstname', 'like',   $search . '%')
                ->orwhere('code', 'like', '%' . $search . '%')
                ->orwhere('customer_phone', 'like', '%' . $search . '%');
        });
    }
    
    // public function scopePhone($query, $search)
    // {
    //     return $query->where('customer_phone', 'like', '%' . $search . '%');
    // }

    // public function scopeCode($query, $search)
    // {
    //     return $query->where('code', 'like', '%' . $search . '%');
    // }
}
