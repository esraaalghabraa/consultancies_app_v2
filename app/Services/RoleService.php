<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

/**
 * Service class for managing user roles.
 */
class RoleService
{
    /**
     * Assign a role to a user.
     *
     * This method finds a role by its name and assigns it to the specified user.
     *
     * @param User $user The user to whom the role will be assigned.
     * @param string $roleName The name of the role to assign to the user.
     * @return void
     */
    public function assignRole(User $user, string $roleName)
    {
        // Retrieve the role from the database by its name.
        $role = Role::where('name', $roleName)->first();

        // Assign the retrieved role to the user.
        $user->addRole($role);
    }
}
