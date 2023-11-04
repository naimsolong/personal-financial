<?php

namespace App\Services;

use App\Models\CategoryGroup;
use App\Models\Workspace;
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

    public function destroy(mixed $model): bool
    {
        $this->setModel($model);

        app(WorkspaceService::class)->current()->detachCategoryGroup($this->getModel()->id);

        return true;
    }

    // TODO: What happen to transactions and categories if update or destroy
}