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
        Schema::create('tr_call_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained('ms_calls')->onDelete('CASCADE');
            $table->string('day', 10);
            $table->integer('disconnect_call');
            $table->integer('prank_call');
            $table->integer('education_call');
            $table->integer('emergency_call');
            $table->integer('abandoned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_call_details');
    }
};
