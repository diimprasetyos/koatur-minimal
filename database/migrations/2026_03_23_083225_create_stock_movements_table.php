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
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->after('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->after('product_id')->nullable()->constrained()->nullOnDelete();

            // Polymorphic: sale, purchase, adjustment, sale_return, purchase_return
            $table->string('reference_type')->after('user_id')->nullable();
            $table->unsignedBigInteger('reference_id')->after('reference_type')->nullable();

            // in | out
            $table->string('type')->after('reference_id');
            $table->integer('qty')->after('type');
            $table->integer('stock_before')->after('qty');
            $table->integer('stock_after')->after('stock_before');
            $table->text('notes')->after('stock_after')->nullable();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['tenant_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
