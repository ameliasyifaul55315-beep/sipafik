
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->foreignId('campus_id')
                ->nullable()
                ->after('id')
                ->constrained('campuses')
                ->nullOnDelete();
        });

        Schema::table('lab_schedules', function (Blueprint $table) {
            $table->foreignId('campus_id')
                ->nullable()
                ->after('id')
                ->constrained('campuses')
                ->nullOnDelete();
        });

        Schema::table('facility_requests', function (Blueprint $table) {
            $table->foreignId('campus_id')
                ->nullable()
                ->after('id')
                ->constrained('campuses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('facility_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });

        Schema::table('lab_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });

        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });
    }
};
