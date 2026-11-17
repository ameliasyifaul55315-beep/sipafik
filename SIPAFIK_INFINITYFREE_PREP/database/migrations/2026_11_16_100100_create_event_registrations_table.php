<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_registrations')) {
            return;
        }

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Kampus PENYELENGGARA kegiatan (bukan kampus peserta), agar penyelenggara melihat semua pesertanya.
            $table->foreignId('campus_id')->constrained('campuses')->cascadeOnDelete();
            // registered | attended | cancelled
            $table->string('status', 20)->default('registered');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['campus_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
