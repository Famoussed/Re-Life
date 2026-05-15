<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained('shelters')->cascadeOnDelete();
            $table->string('name');
            $table->string('species');
            $table->string('age_estimate');
            $table->string('gender')->default('unknown');
            $table->text('story');
            $table->text('health_status');
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
