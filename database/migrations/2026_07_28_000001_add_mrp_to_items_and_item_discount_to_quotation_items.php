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
        if (!Schema::hasColumn('items', 'mrp')) {
            Schema::table('items', function (Blueprint $table) {
                $table->decimal('mrp', 10, 2)->default(0)->after('unit');
            });
        }

        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'sku')) {
                $table->string('sku')->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('quotation_items', 'mrp')) {
                $table->decimal('mrp', 15, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('quotation_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('rate');
            }
            if (!Schema::hasColumn('quotation_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'mrp')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('mrp');
            });
        }

        Schema::table('quotation_items', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('quotation_items', 'sku')) $colsToDrop[] = 'sku';
            if (Schema::hasColumn('quotation_items', 'mrp')) $colsToDrop[] = 'mrp';
            if (Schema::hasColumn('quotation_items', 'discount_percentage')) $colsToDrop[] = 'discount_percentage';
            if (Schema::hasColumn('quotation_items', 'discount_amount')) $colsToDrop[] = 'discount_amount';
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
