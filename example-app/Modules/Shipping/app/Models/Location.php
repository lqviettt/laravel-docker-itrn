<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Shipping\Database\Factories\LocationFactory;

class Location extends Model
{
    use HasFactory;

    /**
     * primaryKey
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'type', 'parent_id', 'code'];
}
