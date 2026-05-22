<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('medicine_name');
            $table->json('medicine_description')->nullable();
            $table->json('medicine_usage')->nullable();
            $table->json('medicine_side_effects')->nullable();
            $table->string('medicine_slug')->unique()->nullable();
            $table->decimal('medicine_price')->nullable();
            $table->date('medicine_expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
