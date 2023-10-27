<?php

namespace App\Services;

use App\Models\CategoryGroup;
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
        $is_created = parent::store($data);

        $workspace_id = session()->get('current_workspace');

        $model = $this->getModel();

        $model->workspace()->attach($workspace_id);

        return $is_created;
    }

    // TODO: What happen to transactions and categories if update or destroy
}