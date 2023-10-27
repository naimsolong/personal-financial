<?php

namespace App\Services;

use App\Models\CategoryGroup;

class CategoryGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _class: CategoryGroup::class
        );
    }

    // TODO: What happen to transactions and categories if update or destroy
}