<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** DIREKONSTRUKSI dari struktur database aktual (lihat catatan pada migration role). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laboratories')) {
            return;
        }

        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->json('facilities')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
