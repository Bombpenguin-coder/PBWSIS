<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_discountable')->default(true)->after('category_name');
            $table->boolean('has_size_small')->default(false)->after('is_discountable');
            $table->boolean('has_size_medium')->default(false)->after('has_size_small');
            $table->boolean('has_size_large')->default(false)->after('has_size_medium');
            $table->boolean('has_sugar_level')->default(false)->after('has_size_large');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'is_discountable',
                'has_size_small',
                'has_size_medium',
                'has_size_large',
                'has_sugar_level',
            ]);
        });
    }
};