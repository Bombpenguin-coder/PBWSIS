<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ingredient_id');
            
            // 1. Point to 'product_id' (lowercase to match your products migration)
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade');
                  
            // 2. Point to 'ingredient_id'
            $table->foreign('ingredient_id')
                  ->references('ingredient_id')
                  ->on('ingredients')
                  ->onDelete('cascade');
            
            // 3. Pivot quantity column
            $table->decimal('quantity_needed', 8, 2); 
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};