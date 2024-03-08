<?php

namespace App\Services;

use App\Enums\WaitlistStatus;
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
            $this->getModel()->firstOrCreate(
                ['email' => $email]
            )
        );

        $waitlist = $this->getModel();

        if ($waitlist->status == WaitlistStatus::IGNORE) {
            $waitlist->update([
                'status' => WaitlistStatus::PENDING,
            ]);
        }

        return $this->haveModel();
    }
}
