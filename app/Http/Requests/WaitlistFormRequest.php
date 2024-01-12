<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use romanzipp\Turnstile\Rules\TurnstileCaptcha;

class WaitlistFormRequest extends FormRequest
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
        $rules = [
            'email' => 'required|email',
        ];

        if(app()->isProduction()) {
            $rules['token'] = ['required', 'string', new TurnstileCaptcha()];
        }

        return $rules;
    }
}
