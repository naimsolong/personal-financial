<?php

namespace App\Services;

use App\Models\AccountGroup;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class AccountGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _class: AccountGroup::class
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

        app(WorkspaceService::class)->current()->attachAccountGroup($this->getModel()->id);

        return $is_created;
    }

    public function destroy(mixed $model): bool
    {
        $this->setModel($model);

        app(WorkspaceService::class)->current()->detachAccountGroup($this->getModel()->id);

        return true;
    }

    // TODO: What happen to transactions and categories if update or destroy
}