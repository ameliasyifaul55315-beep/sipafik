<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kode laboratorium sebelumnya unik se-sistem, sehingga satu kampus dapat menghalangi
 * kampus lain memakai kode yang sama (mis. LAB-01). Diubah menjadi unik per kampus.
 * Tidak ada data yang diubah atau dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasIndex('laboratories', 'laboratories_code_unique')) {
            Schema::table('laboratories', function (Blueprint $table) {
                $table->dropUnique('laboratories_code_unique');
            });
        }

        if (! Schema::hasIndex('laboratories', 'laboratories_campus_code_unique')) {
            Schema::table('laboratories', function (Blueprint $table) {
                $table->unique(['campus_id', 'code'], 'laboratories_campus_code_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('laboratories', 'laboratories_campus_code_unique')) {
            Schema::table('laboratories', function (Blueprint $table) {
                $table->dropUnique('laboratories_campus_code_unique');
            });
        }

        if (! Schema::hasIndex('laboratories', 'laboratories_code_unique')) {
            Schema::table('laboratories', function (Blueprint $table) {
                $table->unique('code', 'laboratories_code_unique');
            });
        }
    }
};
