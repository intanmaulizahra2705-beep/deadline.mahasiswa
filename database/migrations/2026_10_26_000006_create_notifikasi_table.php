<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tugas_id')->nullable()->constrained('tugas')->cascadeOnDelete();
            $table->string('tipe', 10);          // H-3 | H-1 | JAM | TELAT
            $table->string('judul', 150);
            $table->text('pesan');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            // satu jenis pengingat hanya dikirim sekali per tugas per mahasiswa
            $table->unique(['user_id', 'tugas_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
