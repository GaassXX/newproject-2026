<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_currency', 3)->default('USD')->after('theme');
            $table->decimal('exchange_rate', 15, 6)->nullable()->after('preferred_currency');
            $table->boolean('exchange_rate_auto')->default(true)->after('exchange_rate');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['preferred_currency', 'exchange_rate', 'exchange_rate_auto']);
        });
    }
};
