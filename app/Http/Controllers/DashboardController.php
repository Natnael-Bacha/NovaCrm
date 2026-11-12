<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;

class DashboardController extends Controller
{
    public function index()
    {
        $period = request('period', '7D');
        $dateQuery = function ($query) use ($period) {
            if ($period === '7D') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($period === '30D') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($period === '90D') {
                $query->where('created_at', '>=', now()->subDays(90));
            }
            // 'All' or 'Custom' without dates will just not add a where clause
        };

        $stats = [
            'total_leads' => Lead::where($dateQuery)->count(),
            'new_leads' => Lead::where('current_stage', 'new')->where($dateQuery)->count(),
            'active_leads' => Lead::whereIn('current_stage', [
                'contacted',
                'qualified',
                'site visit',
                'proposal sent',
                'initial payment',
            ])->where($dateQuery)->count(),
            'completed_leads' => Lead::where('current_stage', 'completed')->where($dateQuery)->count(),
            'completed_deals' => Deal::where('payment_status', 'fully_paid')->where($dateQuery)->count(),
            'total_sales' => Deal::where('payment_status', 'fully_paid')->where($dateQuery)->sum('deal_amount'),
            'available_units' => Unit::where('status', 'available')->count(), // Available units are current
            'sold_units' => Unit::where('status', 'sold')->where($dateQuery)->count(),
        ];

        $trends = [
            'total_leads' => $this->getTrend(\App\Models\Lead::class),
            'new_leads' => $this->getTrend(\App\Models\Lead::class, fn($q) => $q->where('current_stage', 'new')),
            'active_leads' => $this->getTrend(\App\Models\Lead::class, fn($q) => $q->whereIn('current_stage', ['contacted', 'qualified', 'site visit', 'proposal sent', 'initial payment'])),
            'completed_leads' => $this->getTrend(\App\Models\Lead::class, fn($q) => $q->where('current_stage', 'completed')),
            'completed_deals' => $this->getTrend(\App\Models\Deal::class, fn($q) => $q->where('payment_status', 'fully_paid')),
            'total_sales' => $this->getTrendSum(\App\Models\Deal::class, 'deal_amount', fn($q) => $q->where('payment_status', 'fully_paid')),
            'available_units' => $this->getTrend(\App\Models\Unit::class, fn($q) => $q->where('status', 'available')),
            'sold_units' => $this->getTrend(\App\Models\Unit::class, fn($q) => $q->where('status', 'sold')),
        ];

        $sparklines = [];
        foreach ($trends as $key => $data) {
            $sparklines[$key] = [
                'path' => $this->generateSparklinePath($data),
                'fill' => $this->generateSparklineFill($data),
            ];
        }

        $recentLeads = Lead::with('agent')
            ->latest()
            ->limit(5)
            ->get();

        $projects = Project::all()
            ->map(function (Project $project) {
                $percentage = $project->total_floors > 0
                    ? round(($project->completed_floors / $project->total_floors) * 100)
                    : 0;

                return [
                    'name' => $project->project_name,
                    'percentage' => $percentage,
                    'completed_floors' => $project->completed_floors,
                    'total_floors' => $project->total_floors,
                ];
            });

        $charts = [
            'unitStatus' => $this->unitStatusChart(),
            'leadPipeline' => $this->leadPipelineChart(),
            'salesTrend' => $this->salesTrendChart(),
            'leadSources' => $this->leadSourcesChart(),
            'projectProgress' => $this->projectProgressChart(),
        ];

        return view('admin.dashboard', compact('stats', 'recentLeads', 'projects', 'charts', 'sparklines'));
    }

    protected function getTrend($modelClass, $condition = null) {
        $startDate = now()->subDays(6)->startOfDay();
        $query = $modelClass::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date');
        
        if ($condition) {
            $condition($query);
        }
        
        $results = $query->pluck('count', 'date');
        
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trend[] = $results[$date] ?? 0;
        }
        return $trend;
    }

    protected function getTrendSum($modelClass, $column, $condition = null) {
        $startDate = now()->subDays(6)->startOfDay();
        $query = $modelClass::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM('.$column.') as sum')
            ->groupBy('date');
        
        if ($condition) {
            $condition($query);
        }
        
        $results = $query->pluck('sum', 'date');
        
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trend[] = $results[$date] ?? 0;
        }
        return $trend;
    }

    protected function generateSparklinePath($data) {
        if (empty($data)) return 'M0 30 L100 30';
        $max = max($data);
        $min = min($data);
        if ($max == $min) {
            $max = $min + 1;
        }
        
        $points = [];
        $count = count($data);
        $xStep = 100 / max(1, $count - 1);
        
        foreach ($data as $index => $value) {
            $x = $index * $xStep;
            $y = 28 - (($value - $min) / ($max - $min)) * 26;
            $points[] = ($index === 0 ? 'M' : 'L') . "$x $y";
        }
        return implode(' ', $points);
    }

    protected function generateSparklineFill($data) {
        $path = $this->generateSparklinePath($data);
        if ($path === 'M0 30 L100 30') return 'M0 30 L100 30 Z';
        return $path . ' L100 30 L0 30 Z';
    }

    protected function unitStatusChart(): array
    {
        $units = Unit::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => $units->keys()->map(fn (string $status) => ucfirst($status))->values()->toArray(),
            'data' => $units->values()->toArray(),
            'colors' => ['#0F286F', '#3B82F6', '#93C5FD', '#60A5FA'],
        ];
    }

    protected function leadPipelineChart(): array
    {
        $data = Lead::selectRaw('current_stage, COUNT(*) as total')
            ->groupBy('current_stage')
            ->orderBy('current_stage')
            ->pluck('total', 'current_stage');

        return [
            'labels' => $data->keys()->map(fn (string $stage) => ucfirst($stage))->values()->toArray(),
            'data' => $data->values()->toArray(),
            'colors' => ['#1A365D', '#2B6CB0', '#4299E1', '#63B3ED', '#90CDF4'],
        ];
    }

    protected function salesTrendChart(): array
    {
        $deals = Deal::where('payment_status', 'fully_paid')->get();

        $salesByMonth = $deals->groupBy(fn (Deal $deal) => $deal->created_at->format('Y-m'))
            ->map(fn ($group) => $group->sum('deal_amount'));

        $months = collect(range(11, 0))->map(fn (int $offset) => now()->subMonths($offset));

        return [
            'labels' => $months->map(fn ($date) => $date->format('M Y'))->toArray(),
            'data' => $months->map(fn ($date) => (int) ($salesByMonth[$date->format('Y-m')] ?? 0))->toArray(),
        ];
    }

    protected function leadSourcesChart(): array
    {
        $sources = Lead::selectRaw('lead_source, COUNT(*) as total')
            ->groupBy('lead_source')
            ->orderBy('lead_source')
            ->pluck('total', 'lead_source');

        return [
            'labels' => $sources->keys()->map(fn (string $source) => ucfirst($source))->values()->toArray(),
            'data' => $sources->values()->toArray(),
            'colors' => ['#0F286F', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE'],
        ];
    }

    protected function projectProgressChart(): array
    {
        $projects = Project::all();

        return [
            'labels' => $projects->map(
                fn (Project $project) => $project->completed_floors.'/'.$project->total_floors.' floors'
            )->toArray(),
            'data' => $projects->map(function (Project $project) {
                return $project->total_floors > 0
                    ? round(($project->completed_floors / $project->total_floors) * 100)
                    : 0;
            })->toArray(),
        ];
    }
}