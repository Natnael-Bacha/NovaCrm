<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeSupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(
            collect($this->all())
                ->map(fn ($value) => is_string($value)
                    ? strip_tags(trim($value))
                    : $value
                )
                ->toArray()
        );
    }

    public function rules(): array
    {
        return [
            'old_supervisor' => [
                'required',
                'exists:users,id',
            ],

            'new_supervisor' => [
                'required',
                'exists:users,id',
            ],

            'new_role' => [
                'required',
                'in:admin,agent,collector',
            ],
        ];
    }
}
