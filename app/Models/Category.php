<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * The roles that belong to the categories.
     */
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(CategoryGroup::class, 'category_pivot')->using(CategoryPivot::class);
    }
}
