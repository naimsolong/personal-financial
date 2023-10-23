<?php

namespace App\Actions;

use Illuminate\Http\Request;

class UpdateWorkspace {
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function __invoke(Request $request, $next)
    {
        session()->push('current_workspace', $request->user()?->workspaces()->first()->id);

        return $next($request);
    }
}