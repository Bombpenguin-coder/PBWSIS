<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0.00)->after('sale_date');
            }
            if (!Schema::hasColumn('sales', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0.00)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            if (Schema::hasColumn('sales', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
        });
    }
};