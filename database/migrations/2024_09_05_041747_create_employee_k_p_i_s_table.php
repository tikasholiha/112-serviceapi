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
        Schema::create('tr_employee_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('ms_employees');
            $table->string('period', 50);
            $table->year('year');
            $table->float('calm');
            $table->float('fast');
            $table->float('dispatch');
            $table->float('sosialization');
            $table->float('greating_opening');
            $table->float('greating_closing');
            $table->float('activity');
            $table->float('loyal');
            $table->float('late');
            $table->float('clean');
            $table->float('take_break');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_employee_kpi');
    }
};
