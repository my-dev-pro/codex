<?php

namespace App\Policies;

use App\Enum\Role;
use App\Models\TestRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TestRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TestRequest $testRequest): bool
    {
        return in_array($user->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::GENETICIST->value, Role::DOCTOR->value, Role::SUPER_MODERATOR->value,])
            ?? false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::DOCTOR->value, Role::SUPER_MODERATOR->value,])
            ?? false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TestRequest $testRequest): bool
    {
        return in_array($user->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::GENETICIST->value, Role::SUPER_MODERATOR->value,])
            ?? false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TestRequest $testRequest): bool
    {
        return in_array($user->role, [Role::ADMIN->value])
            ?? false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TestRequest $testRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TestRequest $testRequest): bool
    {
        return in_array($user->role, [Role::ADMIN->value])
            ?? false;
    }
}
