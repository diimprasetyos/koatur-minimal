<?php

declare(strict_types=1);

namespace App\Policies\Return;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Return\SaleReturn;
use Illuminate\Auth\Access\HandlesAuthorization;

class SaleReturnPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SaleReturn');
    }

    public function view(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('View:SaleReturn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SaleReturn');
    }

    public function update(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('Update:SaleReturn');
    }

    public function delete(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('Delete:SaleReturn');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SaleReturn');
    }

    public function restore(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('Restore:SaleReturn');
    }

    public function forceDelete(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('ForceDelete:SaleReturn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SaleReturn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SaleReturn');
    }

    public function replicate(AuthUser $authUser, SaleReturn $saleReturn): bool
    {
        return $authUser->can('Replicate:SaleReturn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SaleReturn');
    }

}