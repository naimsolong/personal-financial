<?php

namespace App\Models;

use App\Models\Traits\CategoryCodeFilter;
use App\Models\Traits\SystemFlagFilter;
use App\Models\Traits\TransactionsTypeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use TransactionsTypeFilter, SystemFlagFilter, CategoryCodeFilter, HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'icon',
        'type',
        'only_system_flag',
    ];

    /**
     * The category that belong to the group.
     */
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(CategoryGroup::class, 'category_pivot')
            ->using(CategoryPivot::class)
            ->as('details')
            ->withTimestamps();
    }

    /**
     * Get the transactions for this category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
