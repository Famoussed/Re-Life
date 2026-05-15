<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('shelter_id')->constrained('shelters')->restrictOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->foreignId('need_id')->nullable()->constrained('needs')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('TRY');
            $table->boolean('is_anonymous')->default(false);
            $table->json('payment_meta');
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
