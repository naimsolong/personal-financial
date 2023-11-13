<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Transaction;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class WorkspaceService extends BaseService
{
    public const KEY = 'current_workspace';
    
    public function __construct()
    {
        parent::__construct(
            _class: Workspace::class
        );
    }

    /**
     * store workspace function
     */
    public function store(Collection $data): bool
    {
        $return = parent::store($data);

        $this->attachUser(auth()->user()->id);

        return $return;
    }
    
    /**
     * destroy workspace function
     */
    public function destroy(mixed $model): bool
    {
        $this->setModel($model)->detachUser(auth()->user()->id);

        return parent::destroy($model);
    }
    
    /**
     * initiate workspace function to store current_workspace in session
     */
    public function initiate(): static
    {
        $workspace_id = request()->user()?->workspaces()->first()?->id;
        
        if(is_null($workspace_id)) {
            throw new ServiceException('Current Workspace not found');
        }

        session()->put(self::KEY, $workspace_id);

        $model = $this->getModel()->where('id', $workspace_id)->first();

        $this->setModel($model);

        return $this;
    }

    public function current(): static
    {
        $workspace_id = session()->get(self::KEY);
        
        if(is_null($workspace_id)) {
            throw new ServiceException('Current Workspace not found');
        }

        $model = $this->getModel()->where('id', $workspace_id)->first();

        $this->setModel($model);

        return $this;
    }
    
    /**
     * change workspace function to update current_workspace in session
     */
    public function change(int $workspace_id): static
    {
        session()->put(self::KEY, $workspace_id);

        $model = $this->getModel()->where('id', $workspace_id)->first();

        $this->setModel($model);

        return $this;
    }

    public function haveUser(?int $user_id = null): bool
    {
        $workspace = $this->getModel();

        return $workspace->users()->when(!is_null($user_id), function($query) use ($user_id){
            $query->where('user_id', $user_id);
        })->exists();
    }

    public function haveCategoryGroup(?int $category_group_id = null): bool
    {
        $workspace = $this->getModel();

        return $workspace->categoryGroups()->when(!is_null($category_group_id), function($query) use ($category_group_id){
            $query->where('category_group_id', $category_group_id);
        })->exists();
    }

    public function haveAccountGroup(?int $account_group_id = null): bool
    {
        $workspace = $this->getModel();

        return $workspace->accountGroups()->when(!is_null($account_group_id), function($query) use ($account_group_id){
            $query->where('account_group_id', $account_group_id);
        })->exists();
    }

    public function haveTransaction(?int $transaction_id = null): bool
    {
        $workspace = $this->getModel();

        return Transaction::when(!is_null($transaction_id), function($query) use ($transaction_id){
            $query->where('id', $transaction_id);
        })->where('workspace_id', $workspace->id)->exists();
    }
    
    /**
     * attach user with current workspace
     */
    public function attachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        $workspace->users()->attach($user_id);
    }
    
    /**
     * detach user from current workspace
     */
    public function detachUser(int $user_id): void
    {
        $workspace = $this->getModel();

        $workspace->users()->detach($user_id);
    }

    public function attachCategoryGroup(mixed $category_group): void
    {
        $workspace = $this->getModel();

        $workspace->categoryGroups()->attach($category_group);
    }

    public function detachCategoryGroup(mixed $category_group): void
    {
        $workspace = $this->getModel();

        $workspace->categoryGroups()->detach($category_group);
    }

    public function attachAccountGroup(mixed $account_group): void
    {
        $workspace = $this->getModel();

        $workspace->accountGroups()->attach($account_group);
    }

    public function detachAccountGroup(mixed $account_group): void
    {
        $workspace = $this->getModel();

        $workspace->accountGroups()->detach($account_group);
    }
}