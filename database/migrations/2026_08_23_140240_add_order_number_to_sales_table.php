<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sales', 'order_number')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('order_number')->nullable()->after('sale_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales', 'order_number')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('order_number');
            });
        }
    }
};