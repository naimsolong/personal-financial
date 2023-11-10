<?php

use App\Enums\AccountsType;
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
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', AccountsType::getAllValues());
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::create('workspace_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained();
            $table->foreignId('account_group_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_accounts');
        Schema::dropIfExists('account_groups');
    }
};
