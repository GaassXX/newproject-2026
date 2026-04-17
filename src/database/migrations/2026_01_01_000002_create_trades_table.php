<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('instrument');
            $table->enum('type', ['buy', 'sell']);
            $table->enum('market', ['crypto', 'forex', 'saham'])->default('crypto');
            $table->decimal('entry_price', 15, 6);
            $table->decimal('exit_price', 15, 6)->nullable();
            $table->decimal('stop_loss', 15, 6)->nullable();
            $table->decimal('take_profit', 15, 6)->nullable();
            $table->decimal('lot_size', 10, 4)->default(1);
            $table->decimal('pnl', 15, 2)->nullable();
            $table->decimal('risk_reward', 8, 2)->nullable();
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');
            $table->string('strategy')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('trades'); }
};
