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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            $table->string('reference_number')->unique();  // PO-20260323-0001
            $table->string('supplier_invoice')->nullable();

            $table->date('purchase_date');
            $table->date('due_date')->nullable();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('due', 12, 2)->default(0); // sisa hutang

            // draft | ordered | received | partial | cancelled
            $table->string('status')->default('received');
            // unpaid | partial | paid
            $table->string('payment_status')->default('paid');

            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->integer('qty');
            $table->integer('qty_received')->default(0);
            $table->decimal('cost_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
