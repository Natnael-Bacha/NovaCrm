<?php

namespace App\Filament\Widgets;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Unit;
use Filament\Widgets\Widget;

class CRMStats extends Widget
{
    protected string $view = 'filament.widgets.crm-stats';

    protected int|string|array $columnSpan = 'full';

    public function getStats(): array
    {
        return [
            [
                'label' => 'Total Leads',
                'value' => number_format(Lead::count()),
                'description' => 'All leads in CRM',
            ],

            [
                'label' => 'New Leads',
                'value' => number_format(
                    Lead::where('current_stage', 'new')->count()
                ),
                'description' => 'Newly added leads',
            ],

            [
                'label' => 'Active Leads',
                'value' => number_format(
                    Lead::whereIn('current_stage', [
                        'contacted',
                        'qualified',
                        'site visit',
                        'proposal sent',
                        'initial payment',
                    ])->count()
                ),
                'description' => 'Currently in progress',
            ],

            [
                'label' => 'Completed Leads',
                'value' => number_format(
                    Lead::where('current_stage', 'completed')->count()
                ),
                'description' => 'Successfully completed',
            ],

            [
                'label' => 'Completed Deals',
                'value' => number_format(
                    Deal::where('payment_status', 'fully_paid')->count()
                ),
                'description' => 'Fully paid deals',
            ],

            [
                'label' => 'Total Sales',
                'value' => number_format(
                    Deal::where('payment_status', 'fully_paid')
                        ->sum('deal_amount')
                ).' ETB',
                'description' => 'Revenue from fully paid deals',
            ],

            [
                'label' => 'Available Units',
                'value' => number_format(
                    Unit::where('status', 'available')->count()
                ),
                'description' => 'Currently available',
            ],

            [
                'label' => 'Sold Units',
                'value' => number_format(
                    Unit::where('status', 'sold')->count()
                ),
                'description' => 'Already sold',
            ],
        ];
    }
}
