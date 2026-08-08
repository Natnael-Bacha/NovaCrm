<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
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
            'project_id' => [
                'required',
                'exists:projects,id',
            ],

            'unit_number' => [
                'required',
                'string',
                'max:255',
                'unique:units,unit_number',
            ],

            'floor' => [
                'required',
                'integer',
                 'min:1',
            ],

            'unit_type' => [
                'required',
                'in:apartment,penthouse,office_space,commercial,studio,duplex',
            ],

            'size' => [
                'required',
                'numeric',
                 'min:0',
            ],

            'price' => [
                'required',
                'numeric',
                 'min:0',
            ],

            'status' => [
                'required',
                'in:available,reserved,sold',
            ],
        ];
    }
}