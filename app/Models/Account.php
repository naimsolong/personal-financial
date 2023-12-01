<?php

namespace App\Models;

use App\Services\WorkspaceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'type',
    ];

    /**
     * The account that belong to the group.
     */
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(AccountGroup::class, 'account_pivot')
            ->using(AccountPivot::class)
            ->as('details')
            ->withPivot('opening_date', 'starting_balance', 'latest_balance', 'currency', 'notes')
            ->when(!is_null(session()->get(WorkspaceService::KEY)), function ($query) {
                $query->where('workspace_id', session()->get(WorkspaceService::KEY));
            })
            ->withTimestamps();
    }

    /**
     * Get the transactions for this account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
