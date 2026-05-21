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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Subscription dimiliki oleh User (owner toko)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Plan yang sedang dipakai
            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->restrictOnDelete();

            // Status: trial | active | expired | cancelled
            $table->string('status')->default('trial');

            // Waktu mulai dan berakhir subscription
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();  // null = belum expire (lifetime)
            $table->timestamp('cancelled_at')->nullable(); // kapan di-cancel

            // Referensi pembayaran eksternal (Midtrans/Xendit order ID)
            $table->string('payment_reference')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
