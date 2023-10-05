<?php

namespace App\Services;

use App\Services\Interfaces\BasicOperation;

class BaseService implements BasicOperation
{
    public function __construct(
        protected $_model = null
    ) { }

    public function setModel(mixed $model)
    {
        $this->_model = $model;

        return $this;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function store(mixed $model = null, array $data = []): bool
    {
        $this->setModel($model->create($data));
        
        return !is_null($this->getModel());
    }
    
    public function update(mixed $model = null, array $data = []): bool
    {
        $is_updated = $model->update($data);

        if($is_updated)
            $this->setModel($model->fresh());

        return $is_updated;
    }
    
    public function destroy(mixed $model = null): bool
    {
        $is_destroyed = $model->delete();

        if($is_destroyed)
            $this->setModel(null);

        return $is_destroyed;
    }
}