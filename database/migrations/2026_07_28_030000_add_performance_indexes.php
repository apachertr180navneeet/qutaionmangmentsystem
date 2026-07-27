<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Items: compound index for active item search (is_active + name)
        Schema::table('items', function (Blueprint $table) {
            $table->index(['is_active', 'name'], 'items_active_name_index');
        });

        // Customers: compound index for getActiveCustomers() query
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['status', 'company_name'], 'customers_status_company_index');
        });

        // Follow-ups: compound index for dashboard today query + upcoming query
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->index(['follow_up_date', 'status'], 'follow_ups_date_status_index');
        });

        // Quotation items: compound index for item-wise report
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->index(['item_id', 'quotation_id'], 'quotation_items_item_quotation_index');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_active_name_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_status_company_index');
        });

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropIndex('follow_ups_date_status_index');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropIndex('quotation_items_item_quotation_index');
        });
    }
};
