<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->unique();
            $table->string('name');
            $table->decimal('min_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
