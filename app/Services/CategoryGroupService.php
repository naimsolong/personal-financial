<?php

namespace App\Services;

use App\Models\CategoryGroup;
use App\Models\CategoryPivot;
use App\Models\Workspace;
use App\Models\WorkspaceCategoriesPivot;
use Illuminate\Support\Collection;

class CategoryGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _model: CategoryGroup::class
        );
    }

    public function store(Collection $data): bool
    {
        $this->setModel(
            $this->getModel()->firstOrCreate(
                $data->toArray()
            )
        );
        
        $is_created = $this->haveModel();

        WorkspaceCategoriesPivot::create([
            'category_group_id' => $this->getModel()->id,
            'workspace_id' => session()->get(WorkspaceService::KEY)
        ]);

        return $is_created;
    }

    public function update(mixed $model, Collection $data): bool
    {
        $this->verifyModel($model);

        $this->setModel(
            $this->getModel()->firstOrCreate(
                $data->toArray()
            )
        );
        
        $updatedModel = $this->getModel();
        
        if($updatedModel->id != $model->id) {
            WorkspaceCategoriesPivot::where(function($query) use ($model) {
                $query->where('category_group_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_group_id' => $updatedModel->id
            ]);

            CategoryPivot::where(function($query) use ($model) {
                $query->where('category_group_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_group_id' => $updatedModel->id
            ]);
        }

        // TODO: What happen to transactions and categories

        return true;
    }

    public function destroy(mixed $model): bool
    {
        $this->verifyModel($model);

        WorkspaceCategoriesPivot::where([
            'category_group_id' => $model->id,
            'workspace_id' => session()->get(WorkspaceService::KEY)
        ])->delete();

        // TODO: What happen to transactions and categories

        return true;
    }
}