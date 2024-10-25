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

    public function scopeCategoryId($query, $category_id)
    {
        return $query->where('category_id', $category_id);
    }

    public function scopeSearchNameCode($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orwhere('code', 'like', $search . '%');
        });
    }
}
