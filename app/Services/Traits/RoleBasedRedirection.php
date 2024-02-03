<?php

namespace App\Services\Traits;

use App\Enums\UserRole;
use App\Providers\RouteServiceProvider;

trait RoleBasedRedirection
{
    protected function getRoleBasedRedirection(UserRole $role): string
    {
        return $role == UserRole::ADMIN
            ? RouteServiceProvider::ADMIN
            : RouteServiceProvider::HOME;
    }
}
