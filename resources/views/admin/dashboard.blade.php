@extends('layouts.app')

@section('content')

<style>
    /* ================================
       CRM DASHBOARD - MODERN
       ================================ */

    :root {
        --brand: #0F286F;
    }

    body {
        background-color: #F8FAFC;
    }

    /* ---------- Typography & Base ---------- */
    .dashboard-container {
        font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
    }



    /* ---------- Stat Cards ---------- */
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .sparkline-svg {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100px;
        height: 50px;
        pointer-events: none;
    }

</style>


<div class="dashboard-container max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Top Greeting Section --}}
    <div class="bg-gradient-to-r from-white to-[#F0F4FF] rounded-2xl p-6 sm:p-8 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center border border-white shadow-sm relative overflow-hidden">
        <!-- Abstract shape background -->
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 pointer-events-none"></div>

        <div class="relative z-10 mb-4 sm:mb-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1">Welcome {{ Auth::user()->full_name ?? 'User' }}</h1>
            <p class="text-slate-500 text-sm sm:text-base">Here's what's happening with your real estate business today.</p>
        </div>

        <div class="relative z-10 bg-[#EBF1FF] rounded-xl p-4 min-w-[200px] border border-blue-50">
            <div class="text-sm font-bold text-slate-800">{{ now()->format('D, M j, Y') }}</div>
            <a href="#" class="text-xs text-blue-600 font-medium mt-1 flex items-center gap-1 hover:text-blue-800 transition">
                Keep building your goals 
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>



    {{-- Key Metrics --}}
    @php
        $currentPeriod = request('period', '7D');
    @endphp
    <div class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-slate-800">Key Metrics</h2>
            <div class="flex items-center bg-white rounded-lg p-1 border border-slate-200 shadow-sm text-xs font-medium">
                <a href="{{ request()->fullUrlWithQuery(['period' => '7D']) }}" class="px-4 py-1.5 {{ $currentPeriod == '7D' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 transition' }} rounded-md">7D</a>
                <a href="{{ request()->fullUrlWithQuery(['period' => '30D']) }}" class="px-4 py-1.5 {{ $currentPeriod == '30D' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 transition' }} rounded-md">30D</a>
                <a href="{{ request()->fullUrlWithQuery(['period' => '90D']) }}" class="px-4 py-1.5 {{ $currentPeriod == '90D' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 transition' }} rounded-md">90D</a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'All']) }}" class="px-4 py-1.5 {{ $currentPeriod == 'All' || $currentPeriod == 'Custom' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 transition' }} rounded-md">All Time</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            
            {{-- Total Leads --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Total Leads</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_leads']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-green-500 font-semibold">100%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">All leads in CRM</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['total_leads']['fill'] }}" fill="url(#blue-grad)" />
                    <path d="{{ $sparklines['total_leads']['path'] }}" fill="none" stroke="#3b82f6" stroke-width="2" />
                    <defs>
                        <linearGradient id="blue-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- New Leads --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">New Leads</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['new_leads']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Newly added leads</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['new_leads']['fill'] }}" fill="url(#green-grad)" />
                    <path d="{{ $sparklines['new_leads']['path'] }}" fill="none" stroke="#10b981" stroke-width="2" />
                    <defs>
                        <linearGradient id="green-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Active Leads --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Active Leads</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['active_leads']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-green-500 font-semibold">100%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Currently in progress</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['active_leads']['fill'] }}" fill="url(#purple-grad)" />
                    <path d="{{ $sparklines['active_leads']['path'] }}" fill="none" stroke="#8b5cf6" stroke-width="2" />
                    <defs>
                        <linearGradient id="purple-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Completed Leads --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Completed Leads</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['completed_leads']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Successfully completed</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['completed_leads']['fill'] }}" fill="url(#teal-grad)" />
                    <path d="{{ $sparklines['completed_leads']['path'] }}" fill="none" stroke="#14b8a6" stroke-width="2" />
                    <defs>
                        <linearGradient id="teal-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#14b8a6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#14b8a6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Completed Deals --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Completed Deals</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['completed_deals']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Fully paid deals</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['completed_deals']['fill'] }}" fill="url(#pink-grad)" />
                    <path d="{{ $sparklines['completed_deals']['path'] }}" fill="none" stroke="#ec4899" stroke-width="2" />
                    <defs>
                        <linearGradient id="pink-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#ec4899" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#ec4899" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Total Sales --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Total Sales</div>
                <div class="text-3xl font-bold text-slate-900"><span class="text-xl">$</span> {{ number_format($stats['total_sales']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Revenue from fully paid deals</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['total_sales']['fill'] }}" fill="url(#blue-grad)" />
                    <path d="{{ $sparklines['total_sales']['path'] }}" fill="none" stroke="#3b82f6" stroke-width="2" />
                </svg>
            </div>

            {{-- Available Units --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Available Units</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['available_units']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Currently available</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['available_units']['fill'] }}" fill="url(#orange-grad)" />
                    <path d="{{ $sparklines['available_units']['path'] }}" fill="none" stroke="#f97316" stroke-width="2" />
                    <defs>
                        <linearGradient id="orange-grad" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#f97316" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Sold Units --}}
            <div class="stat-card bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="text-sm font-semibold text-slate-800 mb-1">Sold Units</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($stats['sold_units']) }}</div>
                <div class="flex items-center text-xs mt-2">
                    <svg class="w-3 h-3 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span class="text-slate-500 font-semibold">0%</span>
                </div>
                <div class="text-xs text-slate-400 mt-1">Already sold</div>
                <svg class="sparkline-svg" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="{{ $sparklines['sold_units']['fill'] }}" fill="url(#purple-grad)" />
                    <path d="{{ $sparklines['sold_units']['path'] }}" fill="none" stroke="#8b5cf6" stroke-width="2" />
                </svg>
            </div>

        </div>
    </div>

    {{-- Analytics Section --}}
    <div class="mb-10">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Analytics</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Lead Overview (Using salesTrendChart ID to render a line chart if that's what it was) --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm lg:col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Lead Overview</h3>
                    <select class="text-xs bg-slate-50 border-none rounded-md text-slate-600 font-medium py-1 px-2 focus:ring-0">
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
                <div id="salesTrendChart" class="min-h-[250px]"></div>
            </div>

            {{-- Sales Performance (Using projectProgressChart ID or another) --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm lg:col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Sales Performance</h3>
                    <select class="text-xs bg-slate-50 border-none rounded-md text-slate-600 font-medium py-1 px-2 focus:ring-0">
                        <option>This Month</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div id="projectProgressChart" class="min-h-[250px]"></div>
            </div>

            {{-- Lead Pipeline (Using leadPipelineChart ID) --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm lg:col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Lead Pipeline</h3>
                    <select class="text-xs bg-slate-50 border-none rounded-md text-slate-600 font-medium py-1 px-2 focus:ring-0">
                        <option>This Month</option>
                        <option>All Time</option>
                    </select>
                </div>
                <div id="leadPipelineChart" class="min-h-[250px]"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
             {{-- Other Charts preserved from old layout to not lose them --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Unit Status</h3>
                </div>
                <div id="unitStatusChart" class="min-h-[250px]"></div>
            </div>
             <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Lead Sources</h3>
                </div>
                <div id="leadSourcesChart" class="min-h-[250px]"></div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
    <script>
        window.dashboardCharts = @json($charts);
    </script>
    @vite('resources/js/dashboard.js')
@endpush
