<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class UnitStatusChart extends ChartWidget
{
    protected ?string $heading = 'Unit Status Distribution';
    protected ?string $description = '';

    protected int|string|array $columnSpan = 'full';

    protected function getHeight(): string
    {
        return '450px';
    }

    protected function getData(): array
    {
        $units = Unit::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = $units->sum();

        $backgroundColor = collect($units->keys())->map(function ($status, $index) {
            return match ($index) {
                0 => '#0F286F',
                1 => '#E8EDF5',
                2 => '#3B82F6',
                3 => '#60A5FA',
                default => '#0F286F',
            };
        })->toArray();

        $borderColor = collect($units->keys())->map(function ($status, $index) {
            return match ($index) {
                0 => '#0A1E4F',
                1 => '#D1D9E6',
                2 => '#2563EB',
                3 => '#3B82F6',
                default => '#0A1E4F',
            };
        })->toArray();

        $labels = $units->keys()->map(function ($status, $index) use ($units, $total) {
            $value = $units->values()->toArray()[$index];
            $percentage = round(($value / $total) * 100, 1);
            return ucfirst($status) . " ({$value} - {$percentage}%)";
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Units',
                    'data' => $units->values()->toArray(),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 4,
                    'hoverOffset' => 25,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '35%',
            'maintainAspectRatio' => false,
            'responsive' => true,
            'animation' => [
                'animateRotate' => true,
                'duration' => 2000,
                'easing' => 'easeInOutQuart',
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'align' => 'center',
              
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(15, 40, 111, 0.95)',
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
                            const value = context.parsed;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return " " + value + " units (" + percentage + "%)";
                        }',
                        'title' => 'function(context) {
                            return context[0].label.split(" (")[0];
                        }',
                    ],
                ],
            ],
        ];
    }
}