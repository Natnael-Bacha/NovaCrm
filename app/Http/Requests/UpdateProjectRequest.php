<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
        $project = $this->route('project');

        return [
            'project_name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('projects', 'project_name')
                    ->ignore($project->id),
            ],

            'project_manager' => [
                'required',
                'string',
                'min:3',
            ],

            'location_address' => [
                'required',
                'string',
                'min:3',
            ],

            'total_floors' => [
                'required',
                'integer',
                'min:1',
            ],

            'completed_floors' => [
                'required',
                'integer',
                'min:0',
                'lte:total_floors',
            ],

            'total_units' => [
                'required',
                'integer',
                'min:1',
            ],

            'due_date' => [
                'required',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'completed_floors.lte' => 'Completed floors cannot exceed total floors.',
        ];
    }
}
