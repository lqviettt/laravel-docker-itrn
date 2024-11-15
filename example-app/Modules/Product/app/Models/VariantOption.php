<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Product\Database\Factories\VariantOptionFactory;

class VariantOption extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name'];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
