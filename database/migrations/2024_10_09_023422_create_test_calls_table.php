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
        Schema::create('ms_test_calls', function (Blueprint $table) {
            $table->id();
            $table->date('call_date');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('phone_number');
            $table->integer('duration');
            $table->string('status');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_test_calls');
    }
};
