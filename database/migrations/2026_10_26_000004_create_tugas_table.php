<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            // kelas_id NULL  => tugas pribadi milik mahasiswa (user_id)
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // pembuat tugas
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline')->index();
            $table->unsignedTinyInteger('bobot')->default(10);      // bobot nilai (%)
            $table->unsignedTinyInteger('kesulitan')->default(3);   // 1-5
            $table->decimal('estimasi_jam', 4, 1)->default(2);      // burst time (jam pengerjaan)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
