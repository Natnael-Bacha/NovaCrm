@extends('layouts.app')

@section('content')

<style>

    /* ================================
       CRM DASHBOARD
       ================================ */

    .crm-dashboard {
        background: #f8fafc;
        min-height: calc(100vh - 64px);
    }

    .crm-header h1 {
        color: #0f286f;
    }


    


    /* ================================
       RESPONSIVE STAT GRID
       ================================ */

    @media (max-width: 1024px) {
        .crm-stats .fi-wi-stats-overview {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 640px) {
        .crm-stats .fi-wi-stats-overview {
            grid-template-columns: 1fr !important;
        }
    }


    /* ================================
       CHART CARDS
       ================================ */

    .crm-chart-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 40, 111, 0.04);
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .crm-chart-card:hover {
        box-shadow: 0 8px 20px rgba(15, 40, 111, 0.07);
    }

    .crm-chart-title {
        color: #0f286f;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

</style>


<div class="crm-dashboard">

    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- Page Header --}}
        <div class="crm-header mb-8">

            <h1 class="text-2xl font-bold">
                CRM Dashboard
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Real-time overview of your units and sales
            </p>

        </div>


        {{-- KPI Statistics --}}
        <div class="crm-stats mb-8">

            <x-filament-widgets::widgets
                :widgets="[
                    \App\Filament\Widgets\CRMStats::class,
                ]"
            />

        </div>


        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Unit Status --}}
            <div class="crm-chart-card">

                <div class="crm-chart-title">
                    Unit Status
                </div>

                <x-filament-widgets::widgets
                    :widgets="[
                        \App\Filament\Widgets\UnitStatusChart::class,
                    ]"
                />

            </div>


            {{-- Lead Pipeline --}}
            <div class="crm-chart-card">

                <div class="crm-chart-title">
                    Lead Pipeline
                </div>

                <x-filament-widgets::widgets
                    :widgets="[
                        \App\Filament\Widgets\LeadPipelineChart::class,
                    ]"
                />

            </div>


            {{-- Project Progress --}}
            <div class="crm-chart-card lg:col-span-2">

                <div class="crm-chart-title">
                    Project Progress
                </div>

                <x-filament-widgets::widgets
                    :widgets="[
                        \App\Filament\Widgets\ProjectProgressChart::class,
                    ]"
                />

            </div>

        </div>

    </div>

</div>

@endsection