<?php

namespace App\Models\Traits;

use App\Services\WorkspaceService;

trait WorkspaceFilter
{
    /**
     * This models that belong to the current workspace.
     */
    public function scopeCurrentWorkspace()
    {
        return $this->whereHas('workspace', function ($query) {
            $query->where('workspace_id', session()->get(WorkspaceService::KEY));
        });
    }
}
