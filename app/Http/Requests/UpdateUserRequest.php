<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge(
            collect($this->all())
                ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
                ->toArray()
        );
    }

    public function rules(): array
    {
        $user = $this->route('id');

        return [

            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'lowercase',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')->id),
            ],

            'role' => [
                'nullable',
                'in:admin,supervisor,agent,collector',
            ],

            'supervisor_id' => [
                'nullable',
                'exists:users,id',
            ],

            'monthly_target' => [
                'required',
                'integer',
                'min:0',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (
                $this->role === 'agent' &&
                empty($this->supervisor_id)
            ) {
                $validator->errors()->add(
                    'supervisor_id',
                    'Please assign a supervisor.'
                );
            }
        });
    }
}
