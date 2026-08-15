<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectProgressChart extends ChartWidget
{
    protected ?string $heading = 'Project Completion Progress';

    protected ?string $description = 'Completed floors versus total floors';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $projects = Project::all();

        return [

            'datasets' => [
                [
                    'label' => 'Completion',

                    'data' => $projects->map(function ($project) {

                        return $project->total_floors > 0
                            ? round(($project->completed_floors / $project->total_floors) * 100)
                            : 0;

                    })->toArray(),

                    'backgroundColor' => '#0F286F',

                    'borderRadius' => 8,
                ],
            ],

            'labels' => $projects->map(function ($project) {

                $percentage = $project->total_floors > 0
                    ? round(($project->completed_floors / $project->total_floors) * 100)
                    : 0;

                return $project->project_name.
                    ' ('.
                    $project->completed_floors.
                    '/'.
                    $project->total_floors.
                    ' floors - '.
                    $percentage.
                    '%)';

            })->toArray(),

        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        $projects = Project::all();

        return [

            'indexAxis' => 'y',

            'maintainAspectRatio' => false,

            'scales' => [

                'x' => [

                    'max' => 100,

                    'ticks' => [

                        'callback' => 'function(value) {
                            return value + "%";
                        }',

                    ],

                ],

            ],

            'plugins' => [

                'legend' => [
                    'display' => false,
                ],

                'tooltip' => [

                    'callbacks' => [

                        'label' => 'function(context) {

                            const projects = '.json_encode(
                            $projects->map(function ($project) {

                                return [
                                    'completed' => $project->completed_floors,
                                    'total' => $project->total_floors,
                                ];

                            })
                        ).';

                            let project = projects[context.dataIndex];

                            return project.completed 
                                + "/" 
                                + project.total 
                                + " floors completed (" 
                                + context.raw 
                                + "%)";

                        }',

                    ],

                ],

            ],

        ];
    }

    protected function getHeight(): string
    {
        return '400px';
    }
}
