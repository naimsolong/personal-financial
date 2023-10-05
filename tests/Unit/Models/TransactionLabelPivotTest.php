<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Label;
use App\Models\Transaction;
use App\Models\TransactionLabelPivot;

test('pivot table able to attach, detach and sync', function () {
    Category::factory(rand(5,10))->create();
    Account::factory(rand(5,10))->create();
    
    $labels = Label::factory(rand(5,10))->create();
    $transaction = Transaction::factory()->create();
    $this->assertModelExists($transaction);

    $random = $labels->random();
    $transaction->labels()->attach($random->id);

    expect(TransactionLabelPivot::where(function($query) use ($transaction, $random) {
        $query->where('transaction_id', $transaction->id)->where('label_id', $random->id);
    })->exists())->toBeTrue();

    $transaction->labels()->detach($random->id);
    expect(TransactionLabelPivot::where(function($query) use ($transaction, $random) {
        $query->where('transaction_id', $transaction->id)->where('label_id', $random->id);
    })->exists())->toBeFalse();

    $transaction->labels()->sync($labels->pluck('id'));
    expect(TransactionLabelPivot::where(function($query) use ($transaction, $labels) {
        $query->where('transaction_id', $transaction->id)->whereIn('label_id', $labels->pluck('id'));
    })->exists())->toBeTrue();
    
    $transaction->labels()->sync([$random->id]);
    expect(TransactionLabelPivot::where(function($query) use ($transaction, $random) {
        $query->where('transaction_id', $transaction->id)->whereIn('label_id', [$random->id]);
    })->exists())->toBeTrue();
});
