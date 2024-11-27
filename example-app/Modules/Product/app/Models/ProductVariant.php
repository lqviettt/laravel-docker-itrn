<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

class ProductVariant extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'variant_option_id', 'value', 'quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantOption()
    {
        return $this->belongsTo(VariantOption::class);
    }
}
