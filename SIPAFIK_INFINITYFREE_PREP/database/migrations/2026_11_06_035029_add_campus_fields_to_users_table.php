<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DIPERBAIKI: versi sebelumnya hanya memanggil unique('username') dan menambah is_active,
 * padahal kolom `username` dan `campus_id` tidak pernah dibuat oleh migration mana pun,
 * sehingga `migrate` pada database kosong gagal. Sekarang setiap kolom dibuat bila belum ada.
 * Database lama tidak terpengaruh karena migration ini sudah tercatat (batch 4).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'campus_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique();
            });
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }

        if (Schema::hasColumn('users', 'campus_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('campus_id');
            });
        }
    }
};
