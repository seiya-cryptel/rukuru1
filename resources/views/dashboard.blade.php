<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold --}} text-xs text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h3>
    </x-slot>

    @if(session('message'))
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ session('message') }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="row justify-content-center mt-3">
                    @livewire('notices')
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="row justify-content-center mt-3">
                    @livewire('applogs')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
