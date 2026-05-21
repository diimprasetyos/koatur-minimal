<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * PermissionSeeder
 *
 * Menggunakan format permission dari Filament Shield: "Action:Model"
 * Dijalankan SETELAH php artisan shield:generate --all
 *
 * CATATAN: RoleSeeder.php sudah TIDAK DIPERLUKAN — semua role
 * dan assignment-nya dikelola di sini.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        // ─────────────────────────────────────────────────────────────
        // DEFINISI PERMISSION
        // ─────────────────────────────────────────────────────────────

        $reportPermissions = [
            'View:PurchaseReturnReportPage',
            'View:PurchasesReportPage',
            'View:QuotationsReportPage',
            'View:SaleReturnReportPage',
            'View:SalesReportPage',
            'View:StockReportPage',
            'View:RevenueStatsWidget',
            'View:SalesChartWidget',
        ];

        $salePermissions = [
            'ViewAny:Sale',
            'View:Sale',
            'Create:Sale',
            'Update:Sale',
            'Delete:Sale',
            'DeleteAny:Sale',
            'Restore:Sale',
            'ForceDelete:Sale',
            'ForceDeleteAny:Sale',
            'RestoreAny:Sale',
            'Replicate:Sale',
            'Reorder:Sale',
        ];

        $saleReturnPermissions = [
            'ViewAny:SaleReturn',
            'View:SaleReturn',
            'Create:SaleReturn',
            'Update:SaleReturn',
            'Delete:SaleReturn',
            'DeleteAny:SaleReturn',
            'Restore:SaleReturn',
            'ForceDelete:SaleReturn',
            'ForceDeleteAny:SaleReturn',
            'RestoreAny:SaleReturn',
            'Replicate:SaleReturn',
            'Reorder:SaleReturn',
        ];

        $productPermissions = [
            'ViewAny:Product',
            'View:Product',
            'Create:Product',
            'Update:Product',
            'Delete:Product',
            'DeleteAny:Product',
            'Restore:Product',
            'ForceDelete:Product',
            'ForceDeleteAny:Product',
            'RestoreAny:Product',
            'Replicate:Product',
            'Reorder:Product',
        ];

        $stockMovementPermissions = [
            'ViewAny:StockMovement',
            'View:StockMovement',
            'Create:StockMovement',
            'Update:StockMovement',
            'Delete:StockMovement',
            'DeleteAny:StockMovement',
            'Restore:StockMovement',
            'ForceDelete:StockMovement',
            'ForceDeleteAny:StockMovement',
            'RestoreAny:StockMovement',
            'Replicate:StockMovement',
            'Reorder:StockMovement',
        ];

        $stockAdjustmentPermissions = [
            'ViewAny:StockAdjustment',
            'View:StockAdjustment',
            'Create:StockAdjustment',
            'Update:StockAdjustment',
            'Delete:StockAdjustment',
            'DeleteAny:StockAdjustment',
            'Restore:StockAdjustment',
            'ForceDelete:StockAdjustment',
            'ForceDeleteAny:StockAdjustment',
            'RestoreAny:StockAdjustment',
            'Replicate:StockAdjustment',
            'Reorder:StockAdjustment',
        ];

        $categoryPermissions = [
            'ViewAny:Category',
            'View:Category',
            'Create:Category',
            'Update:Category',
            'Delete:Category',
            'DeleteAny:Category',
            'Restore:Category',
            'ForceDelete:Category',
            'ForceDeleteAny:Category',
            'RestoreAny:Category',
            'Replicate:Category',
            'Reorder:Category',
        ];

        $supplierPermissions = [
            'ViewAny:Supplier',
            'View:Supplier',
            'Create:Supplier',
            'Update:Supplier',
            'Delete:Supplier',
            'DeleteAny:Supplier',
            'Restore:Supplier',
            'ForceDelete:Supplier',
            'ForceDeleteAny:Supplier',
            'RestoreAny:Supplier',
            'Replicate:Supplier',
            'Reorder:Supplier',
        ];

        $customerPermissions = [
            'ViewAny:Customer',
            'View:Customer',
            'Create:Customer',
            'Update:Customer',
            'Delete:Customer',
            'DeleteAny:Customer',
            'Restore:Customer',
            'ForceDelete:Customer',
            'ForceDeleteAny:Customer',
            'RestoreAny:Customer',
            'Replicate:Customer',
            'Reorder:Customer',
        ];

        $purchasePermissions = [
            'ViewAny:Purchase',
            'View:Purchase',
            'Create:Purchase',
            'Update:Purchase',
            'Delete:Purchase',
            'DeleteAny:Purchase',
            'Restore:Purchase',
            'ForceDelete:Purchase',
            'ForceDeleteAny:Purchase',
            'RestoreAny:Purchase',
            'Replicate:Purchase',
            'Reorder:Purchase',
        ];

        $purchaseReturnPermissions = [
            'ViewAny:PurchaseReturn',
            'View:PurchaseReturn',
            'Create:PurchaseReturn',
            'Update:PurchaseReturn',
            'Delete:PurchaseReturn',
            'DeleteAny:PurchaseReturn',
            'Restore:PurchaseReturn',
            'ForceDelete:PurchaseReturn',
            'ForceDeleteAny:PurchaseReturn',
            'RestoreAny:PurchaseReturn',
            'Replicate:PurchaseReturn',
            'Reorder:PurchaseReturn',
        ];

        $quotationPermissions = [
            'ViewAny:Quotation',
            'View:Quotation',
            'Create:Quotation',
            'Update:Quotation',
            'Delete:Quotation',
            'DeleteAny:Quotation',
            'Restore:Quotation',
            'ForceDelete:Quotation',
            'ForceDeleteAny:Quotation',
            'RestoreAny:Quotation',
            'Replicate:Quotation',
            'Reorder:Quotation',
        ];

        $expensePermissions = [
            'ViewAny:Expense',
            'View:Expense',
            'Create:Expense',
            'Update:Expense',
            'Delete:Expense',
            'DeleteAny:Expense',
            'Restore:Expense',
            'ForceDelete:Expense',
            'ForceDeleteAny:Expense',
            'RestoreAny:Expense',
            'Replicate:Expense',
            'Reorder:Expense',
        ];

        $expenseCategoryPermissions = [
            'ViewAny:ExpenseCategory',
            'View:ExpenseCategory',
            'Create:ExpenseCategory',
            'Update:ExpenseCategory',
            'Delete:ExpenseCategory',
            'DeleteAny:ExpenseCategory',
            'Restore:ExpenseCategory',
            'ForceDelete:ExpenseCategory',
            'ForceDeleteAny:ExpenseCategory',
            'RestoreAny:ExpenseCategory',
            'Replicate:ExpenseCategory',
            'Reorder:ExpenseCategory',
        ];

        $userPermissions = [
            'ViewAny:User',
            'View:User',
            'Create:User',
            'Update:User',
            'Delete:User',
            'DeleteAny:User',
            'Restore:User',
            'ForceDelete:User',
            'ForceDeleteAny:User',
            'RestoreAny:User',
            'Replicate:User',
            'Reorder:User',
        ];

        $rolePermissions = [
            'ViewAny:Role',
            'View:Role',
            'Create:Role',
            'Update:Role',
            'Delete:Role',
            'DeleteAny:Role',
            'Restore:Role',
            'ForceDelete:Role',
            'ForceDeleteAny:Role',
            'RestoreAny:Role',
            'Replicate:Role',
            'Reorder:Role',
        ];

        $tenantPermissions = [
            'ViewAny:Tenant',
            'View:Tenant',
            'Create:Tenant',
            'Update:Tenant',
            'Delete:Tenant',
            'DeleteAny:Tenant',
            'Restore:Tenant',
            'ForceDelete:Tenant',
            'ForceDeleteAny:Tenant',
            'RestoreAny:Tenant',
            'Replicate:Tenant',
            'Reorder:Tenant',
        ];

        // ─────────────────────────────────────────────────────────────
        // SEED SEMUA PERMISSION
        // ─────────────────────────────────────────────────────────────
        $allPermissions = array_merge(
            $reportPermissions,
            $salePermissions,
            $saleReturnPermissions,
            $productPermissions,
            $stockMovementPermissions,
            $stockAdjustmentPermissions,
            $categoryPermissions,
            $supplierPermissions,
            $customerPermissions,
            $purchasePermissions,
            $purchaseReturnPermissions,
            $quotationPermissions,
            $expensePermissions,
            $expenseCategoryPermissions,
            $userPermissions,
            $rolePermissions,
            $tenantPermissions,
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // ─────────────────────────────────────────────────────────────
        // ROLES
        // ─────────────────────────────────────────────────────────────

        // SUPER ADMIN — akses penuh lintas tenant
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);

        // OWNER — akses penuh di panel tenant
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => $guard]);
        $ownerRole->syncPermissions(Permission::all());

        // KASIR — laporan, sales, sale return, view produk & stok
        $kasirPermissions = array_merge(
            $reportPermissions,
            $salePermissions,
            $saleReturnPermissions,
            ['ViewAny:Product', 'View:Product'],
            ['ViewAny:StockMovement', 'View:StockMovement'],
        );
        $kasirRole = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => $guard]);
        $kasirRole->syncPermissions($kasirPermissions);

        // GUDANG — produk, stok, pembelian, supplier, kategori
        $gudangPermissions = array_merge(
            $productPermissions,
            $stockMovementPermissions,
            $stockAdjustmentPermissions,
            $categoryPermissions,
            $supplierPermissions,
            $purchasePermissions,
            $purchaseReturnPermissions,
            ['View:StockReportPage', 'View:PurchasesReportPage', 'View:PurchaseReturnReportPage'],
        );
        $gudangRole = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => $guard]);
        $gudangRole->syncPermissions($gudangPermissions);

        // ─────────────────────────────────────────────────────────────
        // ASSIGN ROLES KE USER
        // ─────────────────────────────────────────────────────────────
        $superAdmin = \App\Models\User::where('email', 'admin@test.com')->first();
        $superAdmin?->syncRoles('super_admin');

        // Assign role owner
        \App\Models\User::where('email', 'owner@test.com')
            ->first()
            ?->syncRoles('owner');
    }
}
