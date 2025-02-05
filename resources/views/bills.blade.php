<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-xs text-gray-800 leading-tight">
        {{ __('Bill Export') }}
        </h3>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="row justify-content-center mt-3">
                    @livewire('bills')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
