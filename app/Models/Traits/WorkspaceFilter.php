<?php

namespace App\Models\Traits;

trait WorkspaceFilter {
    /**
     * This models that belong to the current workspace.
     */
    public function scopeCurrentWorkspace()
    {
        return $this->whereHas('workspace', function($query) {
            $query->where('workspace_id', session()->get('current_workspace'));
        });
    }
}