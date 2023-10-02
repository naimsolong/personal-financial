<?php

namespace App\Http\Requests;

use App\Enums\AccountsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'account_group' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in([AccountsType::ASSETS->value, AccountsType::LIABILITIES->value])],
            'opening_date' => ['required', 'date_format:d/m/Y'],
            'starting_balance' => ['required'],
            'currency' => ['required'],
            'notes' => ['nullable'],
        ];
    }
}
