<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('instrument');
            $table->enum('side', ['buy', 'sell']);
            $table->decimal('volume', 10, 2)->default(0.01);
            $table->decimal('take_profit', 10, 5)->nullable();
            $table->decimal('stop_loss', 10, 5)->nullable();
            $table->string('status')->default('pending'); // pending, sent, executed, cancelled
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
