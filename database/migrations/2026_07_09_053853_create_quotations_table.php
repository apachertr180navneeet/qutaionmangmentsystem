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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('quotation_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('revision_number')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('quotations');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->enum('tax_type', ['cgst_sgst', 'igst', 'none'])->default('cgst_sgst');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('cgst_percentage', 5, 2)->default(0);
            $table->decimal('sgst_percentage', 5, 2)->default(0);
            $table->decimal('igst_percentage', 5, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('round_off', 10, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'approved', 'expired', 'rejected'])->default('draft');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('quotation_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
