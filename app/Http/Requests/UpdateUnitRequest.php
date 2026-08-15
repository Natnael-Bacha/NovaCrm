<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
        $unit = $this->route('unit');

        return [
            'project_id' => [
                'required',
                'exists:projects,id',
            ],

            'unit_number' => [
                'required',
                'string',
                'max:255',

                Rule::unique('units')
                    ->where(fn ($query) => $query->where('project_id', $this->project_id)
                    )
                    ->ignore($unit->id),
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! $this->project_id || ! $this->floor) {
                return;
            }

            $project = Project::find($this->project_id);

            if ($project && $this->floor > $project->total_floors) {
                $validator->errors()->add(
                    'floor',
                    'The selected floor exceeds the total floors of the project.'
                );
            }
        });
    }
}
