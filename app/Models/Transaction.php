<?php

namespace App\Models;

use App\Models\Traits\TransactionsTypeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    use TransactionsTypeFilter, HasFactory;
    
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

    /**
     * The labels that belong to this transactions.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'transaction_label_pivot')
            ->using(TransactionLabelPivot::class)
            ->withTimestamps();
    }
}