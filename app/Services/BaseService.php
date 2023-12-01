<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Services\Interfaces\BasicOperation;
use Illuminate\Support\Collection;

// TODO: Optimize this BaseService class
class BaseService implements BasicOperation
{
    protected string $_class = '';

    public function __construct(
        protected mixed $_model = null
    ) {
        if (! is_null($this->_model)) {
            $this->_class = $this->_model;
        }
    }

    public function setModel(mixed $model)
    {
        $this->_model = $model;

        return $this;
    }

    public function getModel()
    {
        $model = null;

        if (is_string($this->_model)) {
            $model = app($this->_model);
        } elseif (! is_null($this->_model)) {
            $model = $this->_model;
        } elseif ($this->_class != '') {
            $model = app($this->_class);
        }

        $this->verifyModel($model);

        return $model;
    }

    public function haveModel(): bool
    {
        return ! is_null($this->_model) && ! is_string($this->_model);
    }

    protected function verifyModel(mixed $model): void
    {
        if (is_string($model) || is_null($model)) {
            throw new ServiceException('Model Not Found');
        }
    }

    public function store(Collection $data): bool
    {
        $this->setModel(
            $this->getModel()->create($data->toArray())
        );

        return ! is_null($this->getModel());
    }

    public function update(mixed $model, Collection $data): bool
    {
        $this->verifyModel($model);

        $is_updated = $model->update($data->toArray());

        if ($is_updated) {
            $this->setModel($model->fresh());
        }

        return $is_updated;
    }

    public function destroy(mixed $model): bool
    {
        $this->verifyModel($model);

        $is_destroyed = $model->delete();

        if ($is_destroyed) {
            $this->setModel(null);
        }

        return $is_destroyed;
    }
}
