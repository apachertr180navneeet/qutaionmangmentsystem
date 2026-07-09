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
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage',
                'discount_amount',
                'taxable_value',
                'tax_percentage',
                'cgst_percentage',
                'sgst_percentage',
                'igst_percentage',
                'cgst_amount',
                'sgst_amount',
                'igst_amount'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0)->nullable();
            $table->decimal('taxable_value', 15, 2)->default(0)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('cgst_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('sgst_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('igst_percentage', 5, 2)->default(0)->nullable();
            $table->decimal('cgst_amount', 15, 2)->default(0)->nullable();
            $table->decimal('sgst_amount', 15, 2)->default(0)->nullable();
            $table->decimal('igst_amount', 15, 2)->default(0)->nullable();
        });
    }
};
