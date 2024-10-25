<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    public function scopeSearchByStatus($query, $status)
    {
        return $query->when(
            !is_null($status),
            fn($query) => $query->where(function ($query) use ($status) {
                $query->where('status', $status);
            })
        );
    }

    public function scopeSearchByCreated($query, $created_by)
    {
        return $query->when(
            !is_null($created_by),
            fn($query) => $query->where(function ($query) use ($created_by) {
                $query->where('created_by', $created_by);
            })
        );
    }
}
