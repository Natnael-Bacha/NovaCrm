<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

    @foreach ($this->getStats() as $stat)

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

            {{-- Stat Label --}}
            <div class="text-sm font-medium text-gray-500">
                {{ $stat['label'] }}
            </div>

            {{-- Stat Value --}}
            <div class="mt-2 text-2xl font-bold text-gray-900">
                {{ $stat['value'] }}
            </div>

            {{-- Stat Description --}}
            <div class="mt-2 text-xs text-gray-500">
                {{ $stat['description'] }}
            </div>

        </div>

    @endforeach

</div>