<?php

namespace App\Services;

use Modules\Product\Models\VariantOption;
use InvalidArgumentException;

class VariantOptionService
{
    /**
     * findOrFail
     *
     * @param  mixed $id
     * @return VariantOption
     */
    public function findOrFail(int $id): VariantOption
    {
        return VariantOption::findOrFail($id);
    }

    /**
     * validate
     *
     * @param  mixed $data
     * @param  mixed $variantOption
     * @return void
     */
    public function validate(array $data, VariantOption $variantOption): void
    {
        if ($variantOption->type === 'color' && $data['value'] !== $variantOption->name) {
            throw new InvalidArgumentException("Invalid color value. Expected: {$variantOption->name}");
        }
    }
}
