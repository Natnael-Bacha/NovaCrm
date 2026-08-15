<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'in:available,reserved,sold',
            ],

            'current_stage' => [
                'required',
                'in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost',
            ],
        ];
    }
}
