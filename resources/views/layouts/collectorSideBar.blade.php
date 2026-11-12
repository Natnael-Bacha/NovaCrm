<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Collector · ' . config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
    @stack('styles')
</head>

<body class="font-sans bg-gray-50">
    <div class="min-h-screen lg:flex">
        <aside class="w-full border-b lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r" style="background-color: #E8EEFC; border-color: #d3ddf5;">
            <div class="px-6 py-6 border-b" style="border-color: #e2e8f0;">
                <div class="text-2xl font-light tracking-wide" style="color: #0F286F;">
                    Nova<span class="font-semibold">CRM</span>
                </div>
                <div class="mt-1 text-xs text-gray-400">Collector Portal</div>
            </div>

            <div class="px-5 py-5 border-b" style="border-color: #e2e8f0;">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->full_name }}</p>
                <p class="text-xs text-gray-500">Collector</p>
            </div>

            <nav class="px-4 py-4">
                <a
                    href="{{ route('collector.deals') }}"
                    class="block px-4 py-2.5 rounded-md text-sm font-medium"
                    style="background-color: white; color: #0F286F;"
                >
                    Deals to Collect
                </a>
            </nav>

            <div class="px-4 py-4 lg:fixed lg:bottom-0 lg:w-64">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-md"
                        style="background-color: #f1f5f9; color: #64748b;"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="p-4 sm:p-6 md:p-10">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
