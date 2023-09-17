<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountPivot extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_group_id',
        'account_id',
        'opening_date',
        'starting_balance',
        'latest_balance',
        'currency',
        'notes',
    ];
}
