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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // kasir yang melayani
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            $table->string('invoice_number')->unique(); // nomor nota, e.g. INV-20260322-0001
            $table->string('payment_method')->default('cash'); // cash, transfer, ewallet

            $table->decimal('subtotal', 12, 2); // total sebelum diskon
            $table->decimal('discount', 12, 2)->default(0); // diskon nominal
            $table->decimal('tax', 12, 2)->default(0); // pajak (PPN, opsional)
            $table->decimal('total', 12, 2); // total akhir yang harus dibayar
            $table->decimal('paid', 12, 2)->default(0); // uang yang dibayarkan
            $table->decimal('change', 12, 2)->default(0); // kembalian

            $table->string('status')->default('paid'); // paid, pending, cancelled
            $table->text('notes')->nullable(); // catatan kasir

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
