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
        Schema::create('naskahs', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_rilis')->nullable()->default(null);
            $table->date('tgl_disetujui')->nullable()->default(null);
            $table->string('pengaju');
            $table->string('judul');
            $table->string('file');
            $table->enum('status_bps_kota', ['Terkirim', 'Rilis'])->default('Terkirim');
            $table->enum('status_bps_prov', ['Belum Ditanggapi', 'Ditolak', 'Disetujui'])->default('Belum Ditanggapi');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naskahs');
    }
};
