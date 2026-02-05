<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->enum('type', ['online', 'offline'])->default('online');
            $table->json('activities')->nullable(); // Rincian kegiatan
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('location_type', ['online', 'offline'])->default('online');
            $table->text('address')->nullable();
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->json('expense_items')->nullable(); // Rincian COGS (item, qty, price)
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['type', 'activities']);
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['location_type', 'address']);
        });
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn('expense_items');
        });
    }
};