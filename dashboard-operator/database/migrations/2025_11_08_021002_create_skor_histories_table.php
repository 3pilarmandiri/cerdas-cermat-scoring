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
        Schema::create('skor_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kelompok_id');
            $table->integer('nilai');
            $table->timestamps();

            $table->foreign('kelompok_id')->references('id')->on('kelompoks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skor_histories');
    }
};
