<?php

namespace App\Models;

use App\Enums\TransactionsType;
use App\Models\Traits\TransactionsTypeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'due_day', 'due_date', 'due_time', 'type_name'
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

    /**
     * Get this transaction's pair transfer.
     */
    public function transfer_pair(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transfer_pair_id', 'id');
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

    /**
     * Scope a query to only expense type.
     */
    public function scopeIsExpense(Builder $query)
    {
        $query->where('type', TransactionsType::EXPENSE);
    }

    /**
     * Scope a query to only income type.
     */
    public function scopeIsIncome(Builder $query)
    {
        $query->where('type', TransactionsType::INCOME);
    }
    /**
     * Scope a query to only transfer type.
     */
    public function scopeIsTransfer(Builder $query)
    {
        $query->where('type', TransactionsType::TRANSFER);
    }
    
    /**
     * Get due_at in term of dat.
     */
    protected function dueDay(): Attribute
    {
        return new Attribute(
            get: fn () => $this->due_at->format('D'),
        );
    }
    
    /**
     * Get due_at in term of date.
     */
    protected function dueDate(): Attribute
    {
        return new Attribute(
            get: fn () => $this->due_at->format('d/m/Y'),
        );
    }
    
    /**
     * Get due_at in term of time.
     */
    protected function dueTime(): Attribute
    {
        return new Attribute(
            get: fn () => $this->due_at->format('H:i'),
        );
    }
    
    /**
     * Get type's name.
     */
    protected function typeName(): Attribute
    {
        return new Attribute(
            get: fn () => TransactionsType::from($this->type)->name,
        );
    }
}