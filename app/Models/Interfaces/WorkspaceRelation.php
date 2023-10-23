<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface WorkspaceRelation {
    public function workspace(): BelongsTo|BelongsToMany;
}