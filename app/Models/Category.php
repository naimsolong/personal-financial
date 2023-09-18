<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
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
