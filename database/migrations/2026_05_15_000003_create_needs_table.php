<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->foreignId('shelter_id')->constrained('shelters')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2);
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
