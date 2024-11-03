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
        Schema::create('ms_emergency', function (Blueprint $table) {
            $table->id();
            $table->string('period', 50);
            $table->date('period_date');
            $table->year('year');
            $table->foreignId('district_id')->nullable()->constrained('ms_districts');
            $table->integer('kecelakaan');
            $table->integer('kebakaran');
            $table->integer('ambulan_gratis');
            $table->integer('pln');
            $table->integer('mobil_jenazah');
            $table->integer('penanganan_hewan');
            $table->integer('keamanan');
            $table->integer('kriminal');
            $table->integer('bencana_alam');
            $table->integer('kdrt');
            $table->integer('gelandangan_tanpa_identitas');
            $table->integer('pipa_pdam_bocor');
            $table->integer('odgj');
            $table->integer('percobaan_bunuh_diri');
            $table->integer('oli_tumpah');
            $table->integer('kabel_menjuntai');
            $table->integer('mobil_derek');
            $table->integer('tiang_rubuh');
            $table->integer('terkunci_dirumah');
            $table->integer('reklame_rubuh');
            $table->integer('orang_tenggelam');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_emergency');
    }
};
