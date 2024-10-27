<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Create
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * update
     *
     * @param  mixed $model
     * @param  mixed $data
     * @return void
     */
    public function update(Model $model, array $data);
    
    /**
     * Delete
     *
     * @param  mixed $model
     * @return void
     */
    public function delete(Model $model);
}
