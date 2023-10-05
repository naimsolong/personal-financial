<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionFormRequest extends FormRequest
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
            'due_date' => ['required'],
            'due_time' => ['required'],
            'type' => ['required'],
            'category' => ['required'],
            'account_from' => ['required'],
            'account_to' => ['required_if:type,T'],
            'amount' => ['required'],
            'currency' => ['required'],
            'currency_rate' => ['required'],
            'status' => ['nullable'],
            'notes' => ['nullable'],
        ];
    }
}
