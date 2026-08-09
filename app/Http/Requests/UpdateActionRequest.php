<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionRequest extends FormRequest
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
            'lead_id' => [
                'required',
                'exists:leads,id',
            ],

            'activity_type' => [
                'required',
                'in:follow_up_call,meeting,property_visit,email',
            ],

            'assigned_to' => [
                'required',
                'exists:users,id',
            ],

            'status' => [
                'required',
                'in:done,on_progress',
            ],

            'scheduled_time' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}

