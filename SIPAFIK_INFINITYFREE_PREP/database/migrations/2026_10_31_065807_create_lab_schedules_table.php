<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** DIREKONSTRUKSI dari struktur database aktual (lihat catatan pada migration role). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_schedules')) {
            return;
        }

        Schema::create('lab_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained('laboratories')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('course_name');
            $table->string('class_name')->nullable();
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('schedule_type', 30)->default('praktikum');
            $table->string('status', 20)->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['laboratory_id', 'schedule_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_schedules');
    }
};
