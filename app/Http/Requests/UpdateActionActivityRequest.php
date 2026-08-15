<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionActivityRequest extends FormRequest
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
            'activity_type' => [
                'required',
                'in:follow_up_call,meeting,property_visit,email',
            ],
        ];
    }
}
