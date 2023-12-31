<?php

namespace App\Models;

use App\Models\Interfaces\WorkspaceRelation;
use App\Models\Traits\WorkspaceFilter;
use App\Services\WorkspaceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountGroup extends Model implements WorkspaceRelation
{
    use HasFactory, SoftDeletes, WorkspaceFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * This account group that belong to the workspace.
     */
    public function workspace(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_accounts')
            ->using(WorkspaceAccountsPivot::class)
            ->withTimestamps();
    }

    /**
     * The group that belong to the account.
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_pivot')
            ->using(AccountPivot::class)
            ->as('details')
            ->withPivot('opening_date', 'starting_balance', 'latest_balance', 'currency', 'notes')
            ->when(! is_null(session()->get(WorkspaceService::KEY)), function ($query) {
                $query->where('workspace_id', session()->get(WorkspaceService::KEY));
            })
            ->withTimestamps();
    }
}
