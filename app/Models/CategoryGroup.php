<?php

namespace App\Models;

use App\Models\Interfaces\WorkspaceRelation;
use App\Models\Traits\SystemFlagFilter;
use App\Models\Traits\TransactionsTypeFilter;
use App\Models\Traits\WorkspaceFilter;
use App\Services\WorkspaceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryGroup extends Model implements WorkspaceRelation
{
    use WorkspaceFilter, TransactionsTypeFilter, SystemFlagFilter, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'only_system_flag',
    ];

    /**
     * This category group that belong to the workspace.
     */
    public function workspace(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_categories')
            ->using(WorkspaceCategoriesPivot::class)
            ->withTimestamps();
    }
    
    /**
     * The group that belong to the categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_pivot')
            ->using(CategoryPivot::class)
            ->as('details')
            ->wherePivot('workspace_id', session()->get(WorkspaceService::KEY))
            ->withTimestamps();
    }
}
