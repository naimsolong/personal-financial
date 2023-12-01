<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\CategoryPivot;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class CategoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _model: Category::class
        );
    }

    public function store(Collection $data): bool
    {
        $model = $this->getModel()->firstOrCreate([
            'name' => $data->get('name'),
            'type' => $data->get('type')
        ]);
        
        $model->group()->attach($data->get('category_group'), [
            'workspace_id' => session()->get(WorkspaceService::KEY),
        ]);

        $this->setModel($model);

        return $this->haveModel();
    }

    public function update(mixed $model, Collection $data): bool
    {
        $this->verifyModel($model);
        
        $categoryGroup = CategoryGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('category_group'));

        $this->setModel(
            $this->getModel()->firstOrCreate(
                $data->only('name', 'type')->toArray()
            )
        );
        
        $updatedModel = $this->getModel();
        
        if($updatedModel->id != $model->id) {
            CategoryPivot::where(function($query) use ($categoryGroup, $model) {
                $query->where('category_group_id', $categoryGroup->id)
                    ->where('category_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_id' => $updatedModel->id
            ]);
            
            Transaction::where(function($query) use ($model) {
                $query->where('category_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_id' => $updatedModel->id
            ]);
        }

        return true;
    }

    public function destroy(mixed $model): bool
    {
        $this->verifyModel($model);

        // TODO: Change transactions category_id to another id

        if($model->transactions()->exists())
            throw new ServiceException('This Category have transactions');

        $model->group()->detach();
        
        $this->setModel(null);

        return true;
    }
}