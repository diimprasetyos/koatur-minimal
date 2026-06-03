<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique('sales_invoice_number_unique');

            // Ganti dengan composite: unik per tenant
            $table->unique(['tenant_id', 'invoice_number'], 'sales_tenant_invoice_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_tenant_invoice_unique');
            $table->unique('invoice_number', 'sales_invoice_number_unique');
        });
    }
};
