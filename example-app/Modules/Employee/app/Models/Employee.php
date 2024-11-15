<?php

namespace Modules\Employee\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends BaseModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'firstname', 'lastname', 'code', 'phone', 'date_of_birth', 'gender', 'start_date', 'orders_sold'];
    protected $hidden = ['created_at','updated_at'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'employee_permissions', 'employee_id', 'permission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearchByNameCode($query, $search)
    {
        return $query->when(
            !is_null($search),
            fn($query) => $query->where(function ($query) use ($search) {
                $query->where('lastname', 'like', $search . '%')
                    ->orWhere('firstname', 'like', $search . '%')
                    ->orWhere('code', 'like', $search . '%');
            })
        );
    }

    public function scopeSearchByPhone($query, $phone)
    {
        return $query->when(
            !is_null($phone),
            fn($query) => $query->where(function ($query) use ($phone) {
                $query->where('phone', 'like', '%' . $phone . '%');
            })
        );
    }
}

