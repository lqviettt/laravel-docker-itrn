<?php

namespace App\Repositories;

use App\Contract\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $_model;

    /**
     * EloquentRepository constructor.
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     * @return string
     */
    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel()
    {
        $this->_model = app()->make(
            $this->getModel()
        );
    }
    
    /**
     * create
     *
     * @param  mixed $data
     * @return void
     */
    public function create(array $data)
    {
        return $this->_model->create($data);
    }
    
    /**
     * update
     *
     * @param  mixed $model
     * @param  mixed $data
     * @return void
     */
    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $model;
    }
    
    /**
     * delete
     *
     * @param  mixed $model
     * @return void
     */
    public function delete(Model $model)
    {
        $model->delete();

        return $model;
    }
}
