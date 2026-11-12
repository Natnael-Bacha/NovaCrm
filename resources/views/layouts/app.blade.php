<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', config('app.name'))
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
    @stack('styles')
    @stack('scripts')
    <style>
        /* Sidebar active state */
        .sidebar-link {
            color: #94a3b8; /* slate-400 */
        }
        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .sidebar-link.active {
            color: #ffffff;
            background-color: #1d4ed8; /* blue-700 */
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-[#F8FAFC]" style="font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;">

    <div class="min-h-screen lg:flex">

        <!-- Mobile overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-[240px] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:relative lg:flex lg:flex-col lg:h-screen lg:sticky lg:top-0"
               style="background-color: #0A163E; border-right: 1px solid #1e293b;">

            <!-- Logo - Fixed at top -->
            <div class="px-6 py-8 flex-shrink-0">
                <div class="text-2xl font-light tracking-wide text-white">
                    Nova<span class="font-bold text-[#3B82F6]">CRM</span>
                </div>
            </div>

            <!-- Navigation - Scrollable middle section -->
            <nav class="flex-1 px-4 py-2 space-y-1.5 overflow-y-auto">

                @php
                    $currentRoute = request()->route()->getName();
                @endphp

                <a href="{{ route('adminDashboard') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'adminDashboard' ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('leads') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'leads' ? 'active' : '' }}">
                    Leads
                </a>

                <a href="{{ route('team.index') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'team.index' ? 'active' : '' }}">
                    Teams
                </a>

                <a href="{{ route('getProjects') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'getProjects' ? 'active' : '' }}">
                    Projects
                </a>

                <a href="{{ route('admin.units') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'admin.units' ? 'active' : '' }}">
                    Units
                </a>

                <a href="{{ route('admin.pipeline') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'admin.pipeline' ? 'active' : '' }}">
                    Pipeline
                </a>

                <a href="{{ route('admin.deals') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'admin.deals' ? 'active' : '' }}">
                    Deals
                </a>

                <a href="{{ route('admin.actions') }}"
                    class="sidebar-link block px-4 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $currentRoute == 'admin.actions' ? 'active' : '' }}">
                    Actions
                </a>

            </nav>

            <!-- Logout -->
            <div class="px-4 py-6 border-t border-slate-800/50 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 flex items-center text-slate-400 hover:text-white hover:bg-white/5">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen lg:ml-0 flex flex-col">
            
            <!-- Top Navigation Bar -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center h-[72px]">
                
                <!-- Mobile Menu Button & Search -->
                <div class="flex items-center flex-1">
                    <button onclick="toggleSidebar()" data-no-loading class="mr-4 lg:hidden p-2 rounded-md hover:bg-slate-100 text-slate-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="relative w-full max-w-md hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" placeholder="Search anything..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-full text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-slate-50 transition-colors">
                    </div>
                </div>

                <!-- Right Side Actions (Date & Profile) -->
                <div class="flex items-center justify-end space-x-6">
                    <div class="hidden md:flex items-center text-sm font-medium text-slate-600">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ now()->format('D, M j, Y') }}
                    </div>
                    
                    <div class="h-6 w-px bg-slate-200 hidden md:block"></div>
                    
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <div class="h-9 w-9 rounded-full bg-[#0A163E] flex items-center justify-center text-white font-semibold shadow-sm">
                            {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-sm font-semibold text-slate-800 leading-tight">{{ Auth::user()->full_name ?? 'User' }}</div>
                            <div class="text-xs text-slate-500">{{ Auth::user()->role ?? 'Admin' }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-x-hidden">
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

    <script>
    (function() {
        var SPINNER = '<svg class="animate-spin h-4 w-4 mr-2 inline-block" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';

        function mark(btn) {
            if (btn.dataset.loading) return;
            btn.dataset.loading = '1';
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = SPINNER + btn.innerHTML;
        }

        function restore(btn) {
            if (!btn.dataset.loading) return;
            btn.innerHTML = btn.dataset.originalHtml;
            btn.disabled = false;
            btn.style.pointerEvents = '';
            delete btn.dataset.loading;
            delete btn.dataset.originalHtml;
        }

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn || btn.disabled || btn.dataset.loading || btn.dataset.noLoading) return;
            if (btn.closest('#sidebar') || btn.closest('#sidebarOverlay')) return;
            if (btn.classList.contains('toast-close') || btn.classList.contains('modal-close') || btn.classList.contains('password-toggle')) return;
            var text = btn.textContent.trim();
            if (text === '✕' || text === '×') return;

            var isSubmit = btn.type === 'submit';
            var hasClick = btn.hasAttribute('onclick') || btn.hasAttribute('x-on:click') || btn.hasAttribute('@click');
            if (!isSubmit && !hasClick) return;

            mark(btn);

            if (isSubmit) {
                btn.style.pointerEvents = 'none';
            } else {
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                setTimeout(function() { restore(btn); }, 500);
            }
        });

        document.addEventListener('submit', function(e) {
            var btn = e.target.querySelector('button[data-loading="1"], input[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
            }
        }, true);

        var originalFetch = window.fetch;
        window.fetch = function() {
            var btns = Array.prototype.slice.call(document.querySelectorAll('button[data-loading="1"]'));
            return originalFetch.apply(this, arguments).then(function(response) {
                btns.forEach(restore);
                return response;
            }, function(err) {
                btns.forEach(restore);
                throw err;
            });
        };
    })();
    </script>

    @stack('scripts')
</body>
</html>