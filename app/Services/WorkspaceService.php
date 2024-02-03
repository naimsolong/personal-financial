<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\AccountGroup;
use App\Models\CategoryGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WorkspaceService extends BaseService
{
    public const KEY = 'current_workspace';

    public function __construct()
    {
        parent::__construct(
            _model: Workspace::class
        );
    }

    /**
     * store workspace function
     */
    public function store(Collection $data): bool
    {
        $return = parent::store($data);

        $this->attach(auth()->user());

        return $return;
    }

    /**
     * destroy workspace function
     */
    public function destroy(mixed $model): bool
    {
        $this->setModel($model)->detach(auth()->user());

        return parent::destroy($model);
    }

    /**
     * initiate workspace function to store current_workspace in session
     */
    public function initiate(): static
    {
        $workspace = request()->user()?->workspaces()->first();

        if (is_null($workspace)) {
            throw new ServiceException('Current Workspace not found');
        }

        $this->setModel($workspace);

        session()->put(self::KEY, $workspace->id);

        return $this;
    }

    /**
     * set current workspace
     */
    public function current(): static
    {
        $workspace_id = session()->get(self::KEY);

        if (is_null($workspace_id)) {
            throw new ServiceException('Current Workspace not found');
        }

        $model = $this->getModel()->where('id', $workspace_id)->first();

        $this->setModel($model);

        return $this;
    }

    /**
     * update current_workspace in session
     */
    public function change(int $workspace_id): static
    {
        session()->put(self::KEY, $workspace_id);

        $model = $this->getModel()->where('id', $workspace_id)->first();

        $this->setModel($model);

        return $this;
    }

    /**
     * check the relation with current workspace
     */
    public function have(User|CategoryGroup|AccountGroup|Transaction|string $relation): bool
    {
        $workspace = $this->getModel();

        if (is_string($relation)) {
            $relation = app($relation);
        }

        [$workspace, $column] = match (get_class($relation)) {
            'App\Models\User' => [$workspace->users(), 'user_id'],
            'App\Models\CategoryGroup' => [$workspace->categoryGroups(), 'category_group_id'],
            'App\Models\AccountGroup' => [$workspace->accountGroups(), 'account_group_id'],
            'App\Models\Transaction' => [$workspace->transactions(), 'transaction_id'],
            default => throw new ServiceException('Relation not found')
        };

        return $workspace->when(isset($relation->id), function ($query) use ($relation, $column) {
            $query->where($column, $relation->id);
        })->exists();
    }

    /**
     * attach the relation with current workspace
     */
    public function attach(User|CategoryGroup|AccountGroup|Builder $relation): static
    {
        $workspace = $this->getModel();

        $workspace = match (true) {
            $relation instanceof User => $workspace->users(),
            $relation instanceof CategoryGroup => $workspace->categoryGroups(),
            $relation instanceof AccountGroup => $workspace->accountGroups(),
            default => throw new ServiceException('Relation not found')
        };

        $workspace->attach($relation->id);

        return $this;
    }

    /**
     * detach user from current workspace
     */
    public function detach(User|CategoryGroup|AccountGroup|Builder $relation): static
    {
        $workspace = $this->getModel();

        $workspace = match (true) {
            $relation instanceof User => $workspace->users(),
            $relation instanceof CategoryGroup => $workspace->categoryGroups(),
            $relation instanceof AccountGroup => $workspace->accountGroups(),
            default => throw new ServiceException('Relation not found')
        };

        $workspace->detach($relation->id);

        return $this;
    }
}
