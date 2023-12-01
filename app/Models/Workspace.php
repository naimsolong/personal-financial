<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Workspace $workspace) {
            $workspace->slug = Str::of($workspace->name.' '.now()->format('ymd').rand(1111,9999))->slug('-')->__toString(); // TODO: Create traits or anything to centralize this function
        });
        
        static::saving(function (Workspace $workspace) {
            $workspace->slug = Str::of($workspace->name.' '.now()->format('ymd').rand(1111,9999))->slug('-')->__toString(); // TODO: Create traits or anything to centralize this function
        });
    }

    /**
     * The users that belong to this workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->using(WorkspaceUsersPivot::class)
            ->withTimestamps();
    }

    /**
     * The account groups that belong to this workspace.
     */
    public function accountGroups(): BelongsToMany
    {
        return $this->belongsToMany(AccountGroup::class, 'workspace_accounts')
            ->using(WorkspaceAccountsPivot::class)
            ->withTimestamps();
    }

    /**
     * The category groups that belong to this workspace.
     */
    public function categoryGroups(): BelongsToMany
    {
        return $this->belongsToMany(CategoryGroup::class, 'workspace_categories')
            ->using(WorkspaceCategoriesPivot::class)
            ->withTimestamps();
    }

    /**
     * The transactions that belong to this workspace.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeCurrentUser()
    {
        return $this->whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        });
    }
}
