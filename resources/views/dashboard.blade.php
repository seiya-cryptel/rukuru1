<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold --}} text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h3>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(session('message'))
                <div class="p-6 text-gray-900">
                    {{ session('message') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
