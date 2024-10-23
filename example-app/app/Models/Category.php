<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'status'];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeSearchName($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
