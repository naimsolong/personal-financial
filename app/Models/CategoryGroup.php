<?php

namespace App\Models;

use App\Models\Traits\SystemFlagFilter;
use App\Models\Traits\TransactionsTypeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryGroup extends Model
{
    use TransactionsTypeFilter, SystemFlagFilter, HasFactory, SoftDeletes;

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
     * The group that belong to the categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_pivot')
            ->using(CategoryPivot::class)
            ->as('details')
            ->withTimestamps();
    }
}
