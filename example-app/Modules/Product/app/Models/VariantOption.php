<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariantOption extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'type',
        'name',
    ];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function scopeSearchByType($query, $type)
    {
        return $query->when(
            !is_null($type),
            fn($query) => $query->where(function ($query) use ($type) {
                $query->where('type', $type);
            })
        );
    }
}
