<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

class Product extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'code', 'quantity', 'price', 'weight', 'description',  'category_id', 'status'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot('quantity', 'price');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function scopeSearchByCategory($query, $categoryId)
    {
        return $query->when(
            !is_null($categoryId),
            fn($query) => $query->where(function ($query) use ($categoryId) {
                $query->where('status', $categoryId);
            })
        );
    }

    public function scopeSearchByNameCode($query, $search)
    {
        return $query->when(
            !is_null($search),
            fn($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', $search . '%');
            })
        );
    }
}
