<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'status'];
    protected $hidden = ['created_at','updated_at'];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->when(
            !is_null($search),
            fn($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like','%' . $search . '%');
            })
        );
    }
}
