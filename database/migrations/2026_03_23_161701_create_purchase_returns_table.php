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
            Schema::create('purchase_returns', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();

                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

                $table->string('reference_number')->unique(); // PR-20260323-0001
                $table->date('return_date');

                $table->decimal('total_return', 12, 2)->default(0);
                // pending | approved | rejected
                $table->string('status')->default('approved');
                // refund | debit_note | replacement
                $table->string('return_method')->default('debit_note');
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
            });

            Schema::create('purchase_return_items', function (Blueprint $table) {
                $table->id();

                $table->foreignId('purchase_return_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('purchase_item_id')->constrained()->cascadeOnDelete();

                $table->integer('qty');
                $table->decimal('cost_price', 12, 2);
                $table->decimal('subtotal', 12, 2);
                $table->text('reason')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
