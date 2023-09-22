<?php

namespace App\Models;

use App\Models\Traits\TransactionsTypeFitler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryGroup extends Model
{
    use TransactionsTypeFitler, HasFactory, SoftDeletes;

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
