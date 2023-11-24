<?php

namespace App\Services;

use App\Models\AccountGroup;
use App\Models\AccountPivot;
use App\Models\Workspace;
use App\Models\WorkspaceAccountsPivot;
use Illuminate\Support\Collection;

class AccountGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _model: AccountGroup::class
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

        WorkspaceAccountsPivot::create([
            'account_group_id' => $this->getModel()->id,
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
            WorkspaceAccountsPivot::where(function($query) use ($model) {
                $query->where('account_group_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'account_group_id' => $updatedModel->id
            ]);

            AccountPivot::where(function($query) use ($model) {
                $query->where('account_group_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'account_group_id' => $updatedModel->id
            ]);
        }

        // TODO: What happen to transactions and accounts

        return true;
    }

    public function destroy(mixed $model): bool
    {
        $this->setModel($model);

        WorkspaceAccountsPivot::where([
            'account_group_id' => $model->id,
            'workspace_id' => session()->get(WorkspaceService::KEY)
        ])->delete();

        // TODO: What happen to transactions and accounts

        return true;
    }
}