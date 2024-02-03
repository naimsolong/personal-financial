<?php

namespace App\Actions;

use App\Enums\UserRole;
use App\Services\Traits\RoleBasedRedirection;
use Illuminate\Http\Request;

class LoginRedirection
{
    use RoleBasedRedirection;

    /**
     * Handle the incoming request.
     *
     * @param  callable  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        $role = $request->user()->role;

        if ($role == UserRole::CUSTOMER) {
            return $next($request);
        }

        return redirect($this->getRoleBasedRedirection($role));
    }
}
