<?php

use App\Enums\TransactionsType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', TransactionsType::getAllValues());
            $table->boolean('only_system_flag')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::create('workspace_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained();
            $table->foreignId('category_group_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_categories');
        Schema::dropIfExists('category_groups');
    }
};
