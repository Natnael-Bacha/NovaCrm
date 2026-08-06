@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">
            CRM Dashboard
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Real‑time overview of your units and sales
        </p>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
        {{-- Unit Status Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="h-50 w-full">
                <x-filament-widgets::widgets
                    :widgets="[
                        \App\Filament\Widgets\UnitStatusChart::class,
                    ]"
                />
            </div>
        </div>

        {{-- Lead Pipeline Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="h-50 w-full">
                <x-filament-widgets::widgets
                    :widgets="[
                        \App\Filament\Widgets\LeadPipelineChart::class,
                    ]"
                />
            </div>
        </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="h-50 w-full">
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