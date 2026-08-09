<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
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

            'unit_id' => [
                'required',
                'exists:units,id',
            ],

            'collector_id' => [
                'required',
                'exists:users,id',
            ],

            'deal_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'down_payment' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_cycle' => [
                'required',
                'in:monthly,quarterly,semi_annually,annually',
            ],

            'number_of_installments' => [
                'required',
                'integer',
                'min:1',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'commission_type' => [
                'required',
                'in:percentage,fixed_amount',
            ],

            'commission_value' => [
                'required',
                'numeric',
                'min:0',
            ],

            'beneficiary' => [
                'required',
                'in:internal_agent,external_agent',
            ],

            'commission_trigger' => [
                'required',
                'in:immediate,each_payment,full_payment',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->down_payment !== null &&
                $this->deal_amount !== null &&
                $this->down_payment > $this->deal_amount
            ) {
                $validator->errors()->add(
                    'down_payment',
                    'Down payment cannot exceed deal amount.'
                );
            }
        });
    }
}