<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            return;
        }

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('campuses')->cascadeOnDelete();
            $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 30)->default('seminar');
            $table->string('location')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('quota')->default(50);
            // campus = hanya terlihat oleh pengguna kampus penyelenggara; public = terlihat mahasiswa kampus lain.
            $table->string('visibility', 20)->default('campus');
            // draft | published | cancelled | completed
            $table->string('status', 20)->default('published');
            $table->timestamps();

            $table->index(['campus_id', 'event_date']);
            $table->index(['status', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
