<?php

namespace App\Services;

class WorkspaceService extends BaseService
{
    public function initiate(): void
    {
        session()->push('current_workspace', request()->user()?->workspaces()->first()->id);
    }

    public function change(int $workspace_id): void
    {
        session()->put('current_workspace', request()->user()?->workspaces()->find($workspace_id)->id);
    }
}