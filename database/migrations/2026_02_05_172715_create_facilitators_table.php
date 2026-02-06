<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilitators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization')->nullable();
            $table->decimal('price', 15, 2); // Daily rate or fee
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilitators');
    }
};