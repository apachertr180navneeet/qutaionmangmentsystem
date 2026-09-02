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
        if (!Schema::hasColumn('quotations', 'show_mrp')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->boolean('show_mrp')->default(true)->after('tax_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('quotations', 'show_mrp')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn('show_mrp');
            });
        }
    }
};
