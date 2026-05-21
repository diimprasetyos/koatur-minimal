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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            // Identitas plan
            $table->string('slug')->unique();   // 'basic', 'pro', 'trial'
            $table->string('name');             // 'Basic', 'Pro', 'Trial'
            $table->text('description')->nullable();

            // Harga (dalam rupiah, integer supaya tidak ada masalah float)
            $table->unsignedBigInteger('price')->default(0); // 0 = gratis
            $table->string('billing_cycle')->default('monthly'); // 'monthly', 'yearly'

            // Batasan fitur
            $table->unsignedInteger('max_tenants')->default(1); // berapa toko yang boleh dibuat
            $table->unsignedInteger('max_users_per_tenant')->default(3);
            $table->unsignedInteger('max_products')->default(100);

            // Fitur tambahan dalam bentuk JSON
            // contoh: {"pos": true, "reports": false, "export": false}
            $table->json('features')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
