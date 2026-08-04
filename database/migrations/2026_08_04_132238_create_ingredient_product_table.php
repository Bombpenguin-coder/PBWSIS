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
    Schema::create('ingredient_product', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
        $table->foreignId('ingredient_id')->constrained('ingredients', 'ingredient_id')->onDelete('cascade');
        $table->decimal('quantity_required', 8, 2); // Amount used per product order
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_product');
    }
};
