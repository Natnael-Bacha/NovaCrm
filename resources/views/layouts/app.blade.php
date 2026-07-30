<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>
    @yield('title', config('app.name'))
</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('scripts')
</head>

<body class="font-sans bg-gray-50">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 flex flex-col h-screen sticky top-0" style="background-color: #f8fafc; border-right: 1px solid #e2e8f0;">

            <!-- Logo - Fixed at top -->
            <div class="px-6 py-8 border-b flex-shrink-0" style="border-color: #e2e8f0;">
                <div class="text-2xl font-light tracking-wide" style="color: #0F286F;">
                    Nova<span style="font-weight: 600; color: #0F286F;">CRM</span>
                </div>
            </div>

            <!-- Navigation - Scrollable middle section -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                @php
                    $currentRoute = request()->route()->getName();
                @endphp

                <a href="{{ route('adminDashboard') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'adminDashboard' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Dashboard
                </a>

                <a href="{{ route('leads') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'leads' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Leads
                </a>

                <a href="{{ route('getSupervisors') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'getSupervisors' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Teams
                </a>
                
                <a href="{{ route('getProjects') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'getProjects' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Projects
                </a>
                     <a href="{{ route('admin.units') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'admin.units' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Units
                </a>
                                </a>
                     <a href="{{ route('admin.pipeline') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'admin.pipeline' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Pipeline
                </a>

                <a href="{{ route('admin.deals') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'admin.deals' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Deals
                </a>
                {{-- <a href="{{ route('reports') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'reports' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Reports
                </a>

                <a href="{{ route('settings') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'settings' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Settings
                </a> --}}

            </nav>

            <!-- Logout - Fixed at bottom with subtle style -->
            <div class="px-4 py-6 border-t flex-shrink-0" style="border-color: #e2e8f0;">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center gap-2"
                        style="background-color: #f1f5f9; color: #64748b;"
                        onmouseenter="this.style.backgroundColor='#e2e8f0'; this.style.color='#0F286F';"
                        onmouseleave="this.style.backgroundColor='#f1f5f9'; this.style.color='#64748b';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>

            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-auto">

            {{ $slot ?? '' }}

            @yield('content')

        </main>

    </div>

</body>
</html>