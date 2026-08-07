<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', config('app.name'))
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @filamentStyles
    @stack('styles')
    @stack('scripts')
</head>

<body class="font-sans bg-gray-50">

    <div class="min-h-screen lg:flex">

        <!-- Mobile overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:relative lg:flex lg:flex-col lg:h-screen lg:sticky lg:top-0"
               style="background-color: #f8fafc; border-right: 1px solid #e2e8f0;">

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

                <a href="{{ route('team.index') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'team.index' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
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

                <a href="{{ route('admin.actions') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'admin.actions' ? 'background-color: #0F286F; color: white;' : 'color: #475569;' }}"
                    onmouseenter="if(!this.style.backgroundColor || this.style.backgroundColor === 'transparent') { this.style.backgroundColor='#f1f5f9'; this.style.color='#0F286F'; }"
                    onmouseleave="if(this.style.backgroundColor === 'rgb(241, 245, 249)' || this.style.backgroundColor === '#f1f5f9') { this.style.backgroundColor='transparent'; this.style.color='#475569'; }">
                    Actions
                </a>

            </nav>

            <!-- Logout -->
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
        <main class="flex-1 min-h-screen lg:ml-0">
            <!-- Mobile Header with Hamburger -->
            <div class="lg:hidden sticky top-0 z-30 bg-white/80 backdrop-blur-sm border-b border-gray-200 px-4 py-3 flex items-center">
                <button onclick="toggleSidebar()" class="p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#0F286F]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="ml-3 text-xl font-light text-[#0F286F]">
                    Nova<span style="font-weight: 600;">CRM</span>
                </div>
            </div>

            <div class="p-4 sm:p-6 md:p-10">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isOpen = sidebar.classList.contains('translate-x-0');

            if (isOpen) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        // Close sidebar when clicking outside on mobile (overlay handles it via onclick)
        // Also close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('translate-x-0')) {
                    toggleSidebar();
                }
            }
        });

        // On window resize, if screen becomes large (lg), ensure sidebar is visible and overlay hidden
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) { // lg breakpoint
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                // On small screens, if sidebar is open, keep it; else ensure it's closed
                // We don't auto-close on resize to avoid bad UX; user can close manually.
                // But we can ensure the initial state is closed.
                // This is handled by the initial classes.
            }
        });

        // Ensure sidebar is closed on small screens when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
            }
        });
    </script>

    @livewireScripts
    @filamentScripts
    @stack('scripts')
</body>
</html>