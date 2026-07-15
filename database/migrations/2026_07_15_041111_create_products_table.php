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
    Schema::create('products', function (Blueprint $table) {
        // Laravel's default primary key (acts as your Product_ID)
        $table->id('product_id'); 
        
        // ERD Attributes
        $table->string('product_name');
        $table->decimal('price', 8, 2); // 8 total digits, 2 decimal places for currency
        $table->integer('stock_quantity')->default(0);
        $table->string('status')->default('Available'); // e.g., Available, Out of Stock
        
        // Laravel's built-in created_at and updated_at timestamps
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
