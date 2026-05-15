<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('license_no')->unique();
            $table->string('city')->index();
            $table->string('phone');
            $table->text('address');
            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelters');
    }
};
