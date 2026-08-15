<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'new_agent' => [
                Rule::requiredIf(fn () => $user->leads()->exists()),
                'nullable',
                'exists:users,id',
            ],
        ];
    }
}
