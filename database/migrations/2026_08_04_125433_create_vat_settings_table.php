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
    Schema::create('vat_settings', function (Blueprint $table) {
        $table->id();
        $table->string('name')->default('Standard VAT');
        $table->decimal('rate', 5, 2)->default(12.00); // e.g., 12.00%
        $table->boolean('is_inclusive')->default(true); // VAT Inclusive or Exclusive
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_settings');
    }
};
