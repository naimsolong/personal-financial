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
            _class: CategoryGroup::class
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

        app(WorkspaceService::class)->current()->attachCategoryGroup($this->getModel()->id);

        return $is_created;
    }

    public function update(mixed $model, Collection $data): bool
    {
        $original_id = $model->id;
        
        $this->setModel(
            $this->getModel()->firstOrCreate(
                $data->toArray()
            )
        );
        
        $is_updated = $this->haveModel();
        
        if($is_updated) {
            WorkspaceCategoriesPivot::where(function($query) use ($original_id) {
                $query->where('category_group_id', $original_id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_group_id' => $this->getModel()->id
            ]);

            CategoryPivot::where(function($query) use ($original_id) {
                $query->where('category_group_id', $original_id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_group_id' => $this->getModel()->id
            ]);
        }

        return $is_updated;
    }

    public function destroy(mixed $model): bool
    {
        app(WorkspaceService::class)->current()->detachCategoryGroup($this->getModel()->id);

        return true;
    }
}