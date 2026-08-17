<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Agent · ' . config('app.name'))
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @filamentStyles
    @stack('styles')
</head>

<body class="font-sans bg-gray-50">

    <div class="min-h-screen lg:flex">

        {{-- Mobile Overlay --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"
            onclick="toggleSidebar()">
        </div>

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64
                   transform -translate-x-full
                   lg:translate-x-0
                   transition-transform duration-300 ease-in-out
                   lg:relative lg:flex lg:flex-col
                   lg:h-screen lg:sticky lg:top-0"
            style="background-color: #f8fafc; border-right: 1px solid #e2e8f0;"
        >

            {{-- Logo --}}
            <div
                class="px-6 py-8 border-b flex-shrink-0"
                style="border-color: #e2e8f0;"
            >
                <div
                    class="text-2xl font-light tracking-wide"
                    style="color: #0F286F;"
                >
                    Nova<span class="font-semibold">CRM</span>
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    Agent Portal
                </div>
            </div>


            {{-- Agent Information --}}
            <div class="px-5 py-5 border-b" style="border-color: #e2e8f0;">

                <div class="flex items-center gap-3">

                    {{-- Avatar --}}
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold"
                        style="background-color: #e8eefc; color: #0F286F;"
                    >
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ auth()->user()->full_name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            Sales Agent
                        </p>
                    </div>

                </div>

            </div>


            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                @php
                    $currentRoute = request()->route()->getName();
                @endphp




                {{-- My Leads --}}
                <a
                    href="{{ route('agent.leads') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'agent.leads'
                        ? 'background-color: #0F286F; color: white;'
                        : 'color: #475569;' }}"
                >
                    

                    Leads
                </a>


                {{-- My Deals --}}
                <a
                    href="{{ route('agent.deals') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'agent.deals'
                        ? 'background-color: #0F286F; color: white;'
                        : 'color: #475569;' }}"
                >
                    

                    Deals
                </a>


                {{-- My Actions --}}
                {{-- <a
                    href="{{ route('agent.actions') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150"
                    style="{{ $currentRoute == 'agent.actions'
                        ? 'background-color: #0F286F; color: white;'
                        : 'color: #475569;' }}"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0M9 5h6"
                        />
                    </svg>

                    My Actions
                </a>
 --}}

               


               

            </nav>


            {{-- Logout --}}
            <div
                class="px-4 py-6 border-t flex-shrink-0"
                style="border-color: #e2e8f0;"
            >
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center gap-2"
                        style="background-color: #f1f5f9; color: #64748b;"
                        onmouseenter="this.style.backgroundColor='#e2e8f0'; this.style.color='#0F286F';"
                        onmouseleave="this.style.backgroundColor='#f1f5f9'; this.style.color='#64748b';"
                    >

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                            />
                        </svg>

                        Logout

                    </button>
                </form>
            </div>

        </aside>


        {{-- Main Content --}}
        <main class="flex-1 min-h-screen">

            {{-- Mobile Header --}}
            <div
                class="lg:hidden sticky top-0 z-30 bg-white/90 backdrop-blur-sm border-b border-gray-200 px-4 py-3 flex items-center"
            >

                <button
                    onclick="toggleSidebar()"
                    class="p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#0F286F]"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>

                <div class="ml-3">
                    <div class="text-xl font-light text-[#0F286F]">
                        Nova<span class="font-semibold">CRM</span>
                    </div>

                    <div class="text-[10px] text-gray-400">
                        Agent Portal
                    </div>
                </div>

            </div>


            {{-- Page Content --}}
            <div class="p-4 sm:p-6 md:p-10">

                {{ $slot ?? '' }}

                @yield('content')

            </div>

        </main>

    </div>


    {{-- Sidebar JavaScript --}}
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


        // Escape key
        document.addEventListener('keydown', function (e) {

            if (e.key === 'Escape') {

                const sidebar = document.getElementById('sidebar');

                if (sidebar.classList.contains('translate-x-0')) {
                    toggleSidebar();
                }
            }

        });


        // Handle resize
        window.addEventListener('resize', function () {

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 1024) {

                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');

                overlay.classList.add('hidden');

                document.body.style.overflow = '';

            }

        });


        // Initial mobile state
        document.addEventListener('DOMContentLoaded', function () {

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