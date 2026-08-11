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
        if (!Schema::hasColumn('items', 'sdp')) {
            Schema::table('items', function (Blueprint $table) {
                $table->decimal('sdp', 10, 2)->default(0)->after('mrp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'sdp')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('sdp');
            });
        }
    }
};
