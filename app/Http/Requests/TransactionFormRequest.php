<?php

namespace App\Http\Requests;

use App\Enums\TransactionsStatus;
use App\Enums\TransactionsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

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
            'due_date' => ['required', 'date_format:d/m/Y'],
            // 'due_time' => ['required', 'date_format:h:i A'], // TODO: Enable this after found timepicker
            'type' => ['required', 'string', new Enum(TransactionsType::class)],
            'category' => ['exclude_if:type,T', 'integer'], // TODO: Check if category type is same transaction type
            'account_from' => ['required', 'integer'],
            'account_to' => ['required_if:type,T', 'integer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', new Enum(CurrencyAlpha3::class)],
            'currency_rate' => ['required', 'numeric'],
            'status' => ['nullable', 'string', new Enum(TransactionsStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
