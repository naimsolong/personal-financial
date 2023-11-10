<?php

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
        Schema::create('account_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces');
            $table->foreignId('account_group_id')->constrained('account_groups');
            $table->foreignId('account_id')->constrained('accounts');
            $table->date('opening_date');
            $table->integer('starting_balance')->default(0);
            $table->integer('latest_balance')->default(0);
            $table->enum('currency', array_column(CurrencyAlpha3::cases(), 'value'));
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_pivot');
    }
};
