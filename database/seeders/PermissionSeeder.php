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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        // ─────────────────────────────────────────────────────────────
        // DEFINISI PERMISSION PER KELOMPOK
        // Format mengikuti Filament Shield: "Action:Model"
        // ─────────────────────────────────────────────────────────────

        // Permission untuk laporan (Report Pages & Widgets)
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

        // Permission untuk Sales (transaksi penjualan)
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

        // Permission untuk Sale Return
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

        // Permission untuk Produk
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

        // Permission untuk Riwayat Stok (Stock Movement)
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

        // Permission untuk Stock Adjustment
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

        // Permission untuk Kategori
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

        // Permission untuk Supplier
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

        // Permission untuk Customer
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

        // Permission untuk Purchase
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

        // Permission untuk Purchase Return
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

        // Permission untuk Quotation
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

        // Permission untuk Expense
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

        // Permission untuk Expense Category
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

        // Permission untuk User & Role management
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
        // ROLES & PERMISSIONS
        // ─────────────────────────────────────────────────────────────

        /**
         * SUPER_ADMIN — akses penuh ke seluruh platform (lintas tenant)
         * Hanya bisa login di panel /superadmin, tidak punya tenant.
         * Shield secara konvensi membebaskan super_admin dari cek permission,
         * tapi role-nya tetap harus ada di DB agar hasRole() bekerja.
         */
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        // Tidak perlu syncPermissions — akses diatur lewat canAccessPanel()
        // dan Shield gate di SuperadminPanelProvider.

        /**
         * ADMIN — akses penuh ke semua permission di panel tenant
         */
        /** @var Role $adminRole */
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $adminRole->syncPermissions(Permission::all());

        /**
         * KASIR — hanya bisa akses:
         *   - Laporan (view only: semua report page & widget)
         *   - Sales (full CRUD)
         *   - Sale Return (full CRUD)
         *   - Produk (view only)
         *   - Riwayat Stok / Stock Movement (view only)
         */
        $kasirPermissions = array_merge(
            // Laporan: hanya view
            $reportPermissions,

            // Sales: full CRUD
            $salePermissions,

            // Sale Return: full CRUD
            $saleReturnPermissions,

            // Produk: hanya view
            [
                'ViewAny:Product',
                'View:Product',
            ],

            // Riwayat Stok: hanya view
            [
                'ViewAny:StockMovement',
                'View:StockMovement',
            ],
        );

        /** @var Role $kasirRole */
        $kasirRole = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => $guard]);
        $kasirRole->syncPermissions($kasirPermissions);

        /**
         * GUDANG — fokus ke operasional stok & pembelian
         */
        $gudangPermissions = array_merge(
            $productPermissions,
            $stockMovementPermissions,
            $stockAdjustmentPermissions,
            $categoryPermissions,
            $supplierPermissions,
            $purchasePermissions,
            $purchaseReturnPermissions,
            // Laporan stok saja
            [
                'View:StockReportPage',
                'View:PurchasesReportPage',
                'View:PurchaseReturnReportPage',
            ],
        );

        /** @var Role $gudangRole */
        $gudangRole = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => $guard]);
        $gudangRole->syncPermissions($gudangPermissions);

        // ─────────────────────────────────────────────────────────────
        // ASSIGN ROLES KE USER
        // ─────────────────────────────────────────────────────────────
        $superAdmin = \App\Models\User::where('email', 'admin@test.com')->first();
        $admin = \App\Models\User::where('email', 'admin@example.com')->first();
        $budi = \App\Models\User::where('email', 'budi@example.com')->first();
        $siti = \App\Models\User::where('email', 'siti@example.com')->first();
        $hendra = \App\Models\User::where('email', 'hendra@example.com')->first();

        $superAdmin?->syncRoles('super_admin');
        $admin?->syncRoles('admin');
        $budi?->syncRoles('kasir');
        $siti?->syncRoles('kasir');
        $hendra?->syncRoles('gudang');
    }
}