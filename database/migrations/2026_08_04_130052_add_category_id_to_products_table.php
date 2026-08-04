<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add the category_id column, nullable initially so it doesn't crash existing products
            $table->unsignedBigInteger('category_id')->nullable()->after('product_name');
            
            // Link it to the categories table
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Safely remove the foreign key and column if we ever need to roll back
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};