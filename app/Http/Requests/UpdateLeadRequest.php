<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
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
        return [
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'lowercase',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'budget_range' => [
                'nullable',
                'string',
                'max:100',
            ],

            'preferred_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'lead_source' => [
                'required',
                'in:website,social media,referral,walk_in,other',
            ],

            'lead_type' => [
                'required',
                'in:buyer,seller,tenant,investor',
            ],

            'current_stage' => [
                'required',
                'in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost',
            ],

            'agent_id' => [
                'nullable',
                Rule::exists('users', 'id')
                    ->where('role', 'agent'),
            ],
        ];
    }
}
