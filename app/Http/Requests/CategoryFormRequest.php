<?php

namespace App\Http\Requests;

use App\Enums\TransactionsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryFormRequest extends FormRequest
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
            'category_group' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in([TransactionsType::EXPENSE->value, TransactionsType::INCOME->value])],
        ];
    }
}
