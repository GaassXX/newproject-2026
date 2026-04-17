<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->string('remote_ticket')->nullable()->after('meta');
            $table->decimal('executed_price', 12, 5)->nullable()->after('remote_ticket');
            $table->timestamp('executed_at')->nullable()->after('executed_price');
            $table->string('executed_by')->nullable()->after('executed_at');
        });
    }

    public function down(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn(['remote_ticket','executed_price','executed_at','executed_by']);
        });
    }
};
