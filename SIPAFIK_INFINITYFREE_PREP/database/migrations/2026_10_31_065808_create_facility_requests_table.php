<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** DIREKONSTRUKSI dari struktur database aktual (lihat catatan pada migration role). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('facility_requests')) {
            return;
        }

        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('laboratory_id')->constrained('laboratories')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('purpose');
            $table->date('request_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('participants_count')->default(1);
            $table->string('status', 20)->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['laboratory_id', 'request_date', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_requests');
    }
};
