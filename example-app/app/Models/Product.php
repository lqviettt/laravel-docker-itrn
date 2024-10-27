<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'code', 'quantity', 'price', 'description',  'category_id', 'status'];
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

    public function scopeSearchByCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    public function scopeSearchByNameCode($query, $search)
    {
        return $query->when(
            !is_null($search),
            fn($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like','%' . $search . '%')
                    ->orWhere('code', 'like', $search . '%');
            })
        );
    }
}
