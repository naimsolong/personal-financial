<?php

namespace App\Services;

use App\Models\Waitlist;

class WaitlistService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _model: Waitlist::class
        );
    }

    public function join(string $email): bool
    {
        $this->setModel(
            $this->getModel()->updateOrCreate(
                ['email' => $email]
            )
        );

        return $this->haveModel();
    }
}
