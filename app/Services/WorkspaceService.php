<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class WorkspaceService extends BaseService
{
    
    public function __construct()
    {
        parent::__construct(
            _class: Workspace::class
        );
    }

    /**
     * store workspace function
     *
     * @param  mixed $data
     * @return bool
     */
    public function store(Collection $data): bool
    {
        $return = parent::store($data);

        $this->attachUser(auth()->user()->id);

        return $return;
    }
    
    /**
     * destroy workspace function
     *
     * @param  mixed $model
     * @return bool
     */
    public function destroy(mixed $model): bool
    {
        $this->setModel($model)->detachUser(auth()->user()->id);

        $return = parent::destroy($model);

        return $return;
    }
    
    /**
     * initiate workspace function to store current_workspace in session
     *
     * @return void
     */
    public function initiate(): void
    {
        session()->put('current_workspace', request()->user()?->workspaces()->first()?->id);
    }
    
    /**
     * change workspace function to update current_workspace in session
     *
     * @param  mixed $workspace_id
     * @return void
     */
    public function change(int $workspace_id): void
    {
        session()->put('current_workspace', $workspace_id);
    }
    
    /**
     * attach user with current workspace
     *
     * @param  mixed $user_id
     * @return void
     */
    public function attachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        $this->verifyModel($workspace);

        $workspace->users()->attach($user_id);
    }
    
    /**
     * detach user from current workspace
     *
     * @param  mixed $user_id
     * @return void
     */
    public function detachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        $this->verifyModel($workspace);

        $workspace->users()->detach($user_id);
    }
}