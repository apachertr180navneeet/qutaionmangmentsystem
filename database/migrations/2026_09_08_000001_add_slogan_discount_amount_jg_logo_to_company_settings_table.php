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
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'slogan')) {
                $table->string('slogan')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('company_settings', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->nullable()->default(0)->after('pan_number');
            }
            if (!Schema::hasColumn('company_settings', 'jg_logo')) {
                $table->string('jg_logo')->nullable()->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('company_settings', 'slogan')) {
                $colsToDrop[] = 'slogan';
            }
            if (Schema::hasColumn('company_settings', 'discount_amount')) {
                $colsToDrop[] = 'discount_amount';
            }
            if (Schema::hasColumn('company_settings', 'jg_logo')) {
                $colsToDrop[] = 'jg_logo';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
