<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use Illuminate\Support\Collection;

class WorkspaceService extends BaseService
{
    public function store(mixed $model = null, Collection $data): bool
    {
        $return = parent::store($model, $data);

        $this->attachUser(auth()->user()->id);

        return $return;
    }

    public function destroy(mixed $model = null): bool
    {
        $this->setModel($model)->detachUser(auth()->user()->id);

        $return = parent::destroy($model);

        return $return;
    }

    public function initiate(): void
    {
        session()->put('current_workspace', request()->user()?->workspaces()->first()->id);
    }

    public function change(int $workspace_id): void
    {
        session()->put('current_workspace', request()->user()?->workspaces()->find($workspace_id)->id);
    }

    public function attachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        if(is_null($workspace))
            throw new ServiceException('Model Not Found');

        $workspace->users()->attach($user_id);
    }

    public function detachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        if(is_null($workspace))
            throw new ServiceException('Model Not Found');

        $workspace->users()->detach($user_id);
    }
}