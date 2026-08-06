<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Deal;
use App\Models\Unit;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CRMStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Total Leads',
                Lead::count()
            )
            ->description('All customers')
            ->icon('heroicon-o-users'),


            Stat::make(
                'Total Deals',
                Deal::count()
            )
            ->description('Successful transactions')
            ->icon('heroicon-o-document-check'),


            Stat::make(
                'Total Revenue',
                number_format(
                    Deal::sum('deal_amount')
                ) . ' ETB'
            )
            ->description('Sales volume')
            ->icon('heroicon-o-banknotes'),


            Stat::make(
                'Available Units',
                Unit::where('status','available')->count()
            )
            ->description('Remaining inventory')
            ->icon('heroicon-o-home'),


        ];
    }
}