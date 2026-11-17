<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DIREKONSTRUKSI: file asli migration ini hilang dari proyek, padahal sudah tercatat
 * di tabel `migrations` (batch 1). Dibuat ulang sesuai struktur database aktual agar
 * instalasi baru (hosting) bisa menjalankan `php artisan migrate` dari nol.
 * Aman untuk database lama: dijaga dengan hasColumn, dan tidak akan dijalankan ulang
 * karena namanya sudah tercatat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('mahasiswa')->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
