<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Services\Interfaces\BasicOperation;
use Illuminate\Support\Collection;

class BaseService implements BasicOperation
{
    public function __construct(
        protected mixed $_model = null
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

    public function store(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $this->setModel($model->create($data->toArray()));
        
        return !is_null($this->getModel());
    }
    
    public function update(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $is_updated = $model->update($data->toArray());

        if($is_updated)
            $this->setModel($model->fresh());

        return $is_updated;
    }
    
    public function destroy(mixed $model = null): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $is_destroyed = $model->delete();

        if($is_destroyed)
            $this->setModel(null);

        return $is_destroyed;
    }
}