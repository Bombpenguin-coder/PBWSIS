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
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            
            // 1. Create the columns explicitly as unsignedBigInteger (matching Laravel's primary key type)
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ingredient_id');
            
            // 2. Explicitly define the foreign key constraints, targeting your specific ERD column names
            $table->foreign('product_id')
                  ->references('Product_ID') // Change this back to 'id' if your products table just uses $table->id()
                  ->on('products')
                  ->onDelete('cascade');
                  
            $table->foreign('ingredient_id')
                  ->references('ingredient_id') // Change this back to 'id' if your ingredients table just uses $table->id()
                  ->on('ingredients')
                  ->onDelete('cascade');
            
            // 3. The specific amount of the ingredient needed for this product
            $table->decimal('quantity_needed', 8, 2); 
            
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
