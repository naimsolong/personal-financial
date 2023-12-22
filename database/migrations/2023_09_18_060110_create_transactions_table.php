<?php

use App\Enums\TransactionsStatus;
use App\Enums\TransactionsType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained();
            $table->timestamp('due_at');
            $table->enum('type', TransactionsType::getAllValues());
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('account_id')->constrained();
            $table->integer('amount');
            $table->enum('currency', array_column(CurrencyAlpha3::cases(), 'value'));
            $table->integer('currency_amount')->nullable();
            $table->integer('currency_rate')->nullable();
            $table->foreignId('transfer_pair_id')->nullable();
            $table->enum('status', TransactionsStatus::getAllValues())->default(TransactionsStatus::NONE->value);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
