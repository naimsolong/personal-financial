<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\CategoryGroup;
use Illuminate\Support\Collection;

class CategoryService extends BaseService
{
    public function store(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $categoryGroup = CategoryGroup::select('id', 'name', 'type')->find($data->get('category_group'));

        $model = $model->firstOrCreate(
            ['name' => $data->get('name')],
            ['type' => $data->get('type')],
        );
        
        $categoryGroup->categories()->attach($model->id);

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function update(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $model->update($data->only('name', 'type')->toArray());

        $model->group()->sync($data->get('category_group'));

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function destroy(mixed $model = null): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $model->group()->detach();
        
        $this->setModel(null);

        return is_null($this->getModel());
    }
}