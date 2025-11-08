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
        Schema::create('kelompoks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pertandingan_id');
            $table->string('kode'); // A, B, C, D
            $table->string('nama_peserta');
            $table->integer('total_skor')->default(0);
            $table->timestamps();

            $table->foreign('pertandingan_id')->references('id')->on('pertandingans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompoks');
    }
};
