<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Support\Collection;

class CategoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _class: Category::class
        );
    }

    public function store(Collection $data): bool
    {
        $categoryGroup = CategoryGroup::select('id', 'name', 'type')->find($data->get('category_group'));

        $model = $this->getModel()->firstOrCreate(
            ['name' => $data->get('name')],
            ['type' => $data->get('type')],
        );
        
        $categoryGroup->categories()->attach($model->id);

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function update(mixed $model, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $is_updated = $model->update($data->only('name', 'type')->toArray());

        $model->group()->sync($data->get('category_group'));

        $this->setModel($model);

        // TODO: What happen to transactions

        return $is_updated;
    }

    public function destroy(mixed $model): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $model->group()->detach();
        
        $this->setModel(null);

        // TODO: What happen to transactions

        return true;
    }
}