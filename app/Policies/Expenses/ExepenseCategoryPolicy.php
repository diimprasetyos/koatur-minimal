<?php

declare(strict_types=1);

namespace App\Policies\Expenses;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Expenses\ExepenseCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExepenseCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExepenseCategory');
    }

    public function view(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('View:ExepenseCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExepenseCategory');
    }

    public function update(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('Update:ExepenseCategory');
    }

    public function delete(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('Delete:ExepenseCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExepenseCategory');
    }

    public function restore(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('Restore:ExepenseCategory');
    }

    public function forceDelete(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('ForceDelete:ExepenseCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExepenseCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExepenseCategory');
    }

    public function replicate(AuthUser $authUser, ExepenseCategory $exepenseCategory): bool
    {
        return $authUser->can('Replicate:ExepenseCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExepenseCategory');
    }

}