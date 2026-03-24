<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('product_name'); // Snapshot nama produk
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2); // Untuk hitung profit
            $table->decimal('discount', 12, 2)->default(0); // Diskon per item
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
