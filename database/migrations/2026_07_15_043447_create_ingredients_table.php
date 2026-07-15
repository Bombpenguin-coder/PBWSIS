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
    Schema::create('ingredients', function (Blueprint $table) {
        $table->id('ingredient_id'); 
        
        $table->string('ingredient_name');
        // Using decimal for quantity/capacity so you can track things like "1.5" kilos or liters
        $table->decimal('quantity', 10, 2)->default(0); 
        $table->string('unit'); // e.g., 'kg', 'liters', 'grams', 'pieces'
        $table->decimal('max_capacity', 10, 2); 
        $table->decimal('reorder_level', 10, 2); // This will power your low-stock notification
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
