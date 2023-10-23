<?php

namespace App\Services;

use App\Actions\Workspace\UpdateWorkspace;
use Illuminate\Http\Request;

class WorkspaceService extends BaseService
{
    public function changeWorkspace(Request $request): void
    {
        session()->put('current_workspace', $request->user()?->workspaces()->find($request->workspace_id)->id);
    }
}