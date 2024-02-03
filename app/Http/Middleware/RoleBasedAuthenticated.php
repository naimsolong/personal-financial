<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Services\Traits\RoleBasedRedirection;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedAuthenticated
{
    use RoleBasedRedirection;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user_role = $request->user()->role;

        if ($user_role != UserRole::fromName(Str::upper($role))) {
            return redirect($this->getRoleBasedRedirection($user_role));
        }

        return $next($request);
    }
}
