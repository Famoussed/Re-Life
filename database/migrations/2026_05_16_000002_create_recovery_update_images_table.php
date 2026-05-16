<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_update_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recovery_update_id')->constrained('recovery_updates')->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_update_images');
    }
};
