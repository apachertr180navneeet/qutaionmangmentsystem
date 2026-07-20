<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unique('uuid', 'quotations_uuid_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('uuid', 'customers_uuid_unique');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unique('uuid', 'items_uuid_unique');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropUnique('quotations_uuid_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_uuid_unique');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('items_uuid_unique');
        });
    }
};
