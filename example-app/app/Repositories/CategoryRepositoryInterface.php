<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{    
    public function select($search, $status);

    public function find(Model $model);
}
