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
        Schema::create('tr_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained('ms_roles');
            $table->foreignId('permission_id')->nullable()->constrained('ms_permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_role_permissions');
    }
};
