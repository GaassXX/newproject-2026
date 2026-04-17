<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('screenshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_id');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->timestamps();
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('screenshots'); }
};
