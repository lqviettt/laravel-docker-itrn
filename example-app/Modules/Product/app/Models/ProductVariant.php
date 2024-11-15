<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Product\Database\Factories\ProductVariantFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_option_id', 'value', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantOption()
    {
        return $this->belongsTo(VariantOption::class);
    }
}
