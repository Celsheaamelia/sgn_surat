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
        Schema::create('riwayatsurat', function (Blueprint $table) {

        $table->id();

        $table->string('nomor_surat')->unique();
        $table->string('perihal');
        $table->date('tanggal');

        $table->foreignId('penandatangan_id')
            ->constrained('penandatangan')
            ->cascadeOnDelete();

        $table->foreignId('tujuan_surat_id')
            ->constrained('tujuan_surats')
            ->cascadeOnDelete();

        $table->foreignId('klasifikasi_surat_id')
            ->constrained('klasifikasi_surat')
            ->cascadeOnDelete();

        $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayatsurat');
    }
};
