<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'due_at',
        'type',
        'category_id',
        'account_id',
        'amount',
        'currency',
        'currency_amount',
        'currency_rate',
        'transfer_pair_id',
        'status',
        'notes',
    ];

    /**
     * Get this transaction's category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get this transaction's account.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // TODO: Add relationship for split transactions

    // TODO: Add relationship with Label
}