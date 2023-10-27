<?php

namespace App\Services;

use App\Models\AccountGroup;

class AccountGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _class: AccountGroup::class
        );
    }

    // TODO: What happen to transactions and categories if update or destroy
}