<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">



                <x-nav-link href="{{ route('compararRSS') }}" :active="request()->routeIs('compararRSS')">
                    {{ __('Comparar el pais') }}
                </x-nav-link>

            </div>
        </div>
    </div>
</x-app-layout>
