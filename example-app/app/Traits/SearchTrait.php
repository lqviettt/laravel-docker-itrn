<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchTrait
{
    public function applySearch(Builder $query, $search, $status = null, $categoryId = null, $created_by = null, $type)
    {
        switch ($type) {
            case 'category':
                $query->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                });
                break;

            case 'product':
                $query->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', $search . '%')
                            ->orWhere('price', 'like', '%' . $search . '%');
                    });
                });

                if ($categoryId) {
                    $query->where('category_id', $created_by);
                }
                break;

            case 'order':
                $query->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('customer_name', 'like', '%' . $search . '%')
                            ->orWhere('customer_phone', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    });
                });

                if ($created_by) {
                    $query->where('created_by', $created_by);
                }
                break;

            default:
                return $query;
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }
}
