<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadPipelineChart extends ChartWidget
{
    protected ?string $heading = 'Lead Pipeline';
    protected ?string $description = '';

    protected function getHeight(): string
    {
        return '450px';
    }

    protected function getData(): array
    {
        $data = Lead::selectRaw('current_stage, COUNT(*) as total')
            ->groupBy('current_stage')
            ->pluck('total', 'current_stage');

        $total = $data->sum();

        // Professional blue palette (navy, slate, muted blues)
        $colors = [
            '#1A365D', // deep navy
            '#2B6CB0', // medium blue
            '#4299E1', // bright blue
            '#63B3ED', // light blue
            '#EBF4FF', // very light (for extra stages)
        ];

        $backgroundColor = collect($data->keys())->map(function ($stage, $index) use ($colors) {
            return $colors[$index % count($colors)];
        })->toArray();

        $borderColor = collect($data->keys())->map(function ($stage, $index) use ($colors) {
            // Slightly darker border for each colour
            return $colors[$index % count($colors)];
        })->toArray();

        $labels = $data->keys()->map(function ($stage, $index) use ($data, $total) {
            $value = $data->values()->toArray()[$index];
            $percentage = round(($value / $total) * 100, 1);
            return ucfirst(str_replace('_', ' ', $stage)) . " ({$value} - {$percentage}%)";
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    // Control bar width – these make bars narrower
                    'barPercentage' => 0.6,      // bar width as fraction of available space
                    'categoryPercentage' => 0.8, // space between bars
                    'hoverBackgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'animation' => [
                'duration' => 2000,
                'easing' => 'easeInOutQuart',
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(26, 54, 93, 0.95)', // matches navy
                    'titleFont' => [
                        'size' => 20,
                        'weight' => '700',
                    ],
                    'bodyFont' => [
                        'size' => 18,
                    ],
                    'padding' => 20,
                    'cornerRadius' => 12,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed.y || context.parsed;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return " " + value + " leads (" + percentage + "%)";
                        }',
                        'title' => 'function(context) {
                            return context[0].label.split(" (")[0];
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0,0,0,0.04)',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => ['size' => 12],
                    ],
                ],
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'font' => ['size' => 12],
                        'maxRotation' => 45, // rotate labels if needed
                    ],
                ],
            ],
        ];
    }
}