<?php

namespace App\Models\Traits;

use App\Enums\SystemCategoryCode;

trait CategoryCodeFilter
{
    public function scopeIsPositiveOpeningBalance()
    {
        return $this->where('code', SystemCategoryCode::OPENING_POSITIVE->value);
    }

    public function scopeIsNegativeOpeningBalance()
    {
        return $this->where('code', SystemCategoryCode::OPENING_NEGATIVE->value);
    }

    public function scopeIsPositiveAdjustment()
    {
        return $this->where('code', SystemCategoryCode::ADJUST_POSITIVE->value);
    }

    public function scopeIsNegativeAdjustment()
    {
        return $this->where('code', SystemCategoryCode::ADJUST_NEGATIVE->value);
    }
}
