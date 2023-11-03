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
        $categoryGroup = CategoryGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('category_group'));

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
        $this->verifyModel($model);

        $categoryGroup = CategoryGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('category_group'));

        $is_updated = $model->update($data->only('name', 'type')->toArray());

        $model->group()->sync($categoryGroup->id);

        $this->setModel($model);

        // TODO: What happen to transactions

        return $is_updated;
    }

    public function destroy(mixed $model): bool
    {
        $this->verifyModel($model);

        $model->group()->detach();
        
        $this->setModel(null);

        // TODO: What happen to transactions

        return true;
    }
}