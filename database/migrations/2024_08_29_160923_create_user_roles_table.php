<?php

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
        Schema::create('tr_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('ms_users')->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained('ms_roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_user_roles');
    }
};
