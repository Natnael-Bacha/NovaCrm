<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'current_stage' => is_string($this->current_stage)
                ? strip_tags(trim($this->current_stage))
                : $this->current_stage,
        ]);
    }

    public function rules(): array
    {
        return [
            'current_stage' => [
                'required',
                'in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost',
            ],
        ];
    }
}
