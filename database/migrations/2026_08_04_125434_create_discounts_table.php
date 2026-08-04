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
    Schema::create('discounts', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., Senior Citizen, PWD, Promo
        $table->enum('type', ['percentage', 'fixed']); // Percentage or Fixed Amount
        $table->decimal('value', 8, 2); // e.g., 20.00 (% or ₱)
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
