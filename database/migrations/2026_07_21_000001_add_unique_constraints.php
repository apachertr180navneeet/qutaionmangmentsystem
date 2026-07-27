<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasIndex('quotations', 'quotations_uuid_unique')) {
                $table->unique('uuid', 'quotations_uuid_unique');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasIndex('customers', 'customers_uuid_unique')) {
                $table->unique('uuid', 'customers_uuid_unique');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasIndex('items', 'items_uuid_unique')) {
                $table->unique('uuid', 'items_uuid_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasIndex('quotations', 'quotations_uuid_unique')) {
                $table->dropUnique('quotations_uuid_unique');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasIndex('customers', 'customers_uuid_unique')) {
                $table->dropUnique('customers_uuid_unique');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasIndex('items', 'items_uuid_unique')) {
                $table->dropUnique('items_uuid_unique');
            }
        });
    }
};
