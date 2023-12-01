<?php

namespace App\Actions\Workspace;

use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class InitiateWorkspace {
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function __invoke(Request $request, $next)
    {
        app(WorkspaceService::class)->initiate();

        return $next($request);
    }
}