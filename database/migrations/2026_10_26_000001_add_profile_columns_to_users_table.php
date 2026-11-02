<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('mahasiswa')->after('email')->index();
            $table->string('nim_nip', 30)->nullable()->after('role');
            $table->boolean('remind_h3')->default(true);
            $table->boolean('remind_h1')->default(true);
            $table->boolean('remind_hours')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->unsignedTinyInteger('jam_per_hari')->default(4);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'nim_nip', 'remind_h3', 'remind_h1', 'remind_hours', 'email_enabled', 'jam_per_hari']);
        });
    }
};
