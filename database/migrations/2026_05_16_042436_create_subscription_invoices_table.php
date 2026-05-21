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
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();

            // Invoice terhubung ke subscription
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Nomor invoice unik, misal: INV-2026050001
            $table->string('invoice_number')->unique();

            // Jumlah yang dibayar (dalam rupiah)
            $table->unsignedBigInteger('amount');

            // Status: pending | paid | failed | refunded
            $table->string('status')->default('pending');

            // Payment gateway yang dipakai: midtrans, xendit, manual
            $table->string('payment_method')->nullable(); // 'midtrans', 'xendit', 'manual'
            $table->string('payment_token')->nullable();  // token dari gateway
            $table->string('payment_url')->nullable();    // URL redirect ke payment page
            $table->json('payment_response')->nullable(); // raw response dari gateway

            // Waktu pembayaran dikonfirmasi
            $table->timestamp('paid_at')->nullable();

            // Periode yang dicakup invoice ini
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
