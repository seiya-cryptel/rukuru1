<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $locale = app()->getLocale();
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    {{-- <div class="max-w-7xl mx-auto px-2 sm:px-2 lg:px-4"> --}}
    <div class="max-w-none mx-auto">
        <div class="flex justify-between h-12">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard', ['locale' => $locale]) }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard', ['locale' => $locale])" :active="request()->routeIs($locale . '/dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                {{-- 勤怠 --}}
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{ name_kintai: '勤怠' }" x-text="name_kintai" x-on:profile-updated.window="name_kintai = $event.detail.name_kintai"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link  :href="route('importkintai', ['locale' => $locale])" :active="request()->routeIs($locale . '/importkintai')" wire:navigate>
                            {{ __('Import Kintai') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('workemployee', ['locale' => $locale])" :active="request()->routeIs($locale . '/workemployee')" wire:navigate>
                            {{ __('Kintai Entry') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('rk', ['locale' => $locale])" :active="request()->routeIs($locale . '/rk')" wire:navigate>
                            {{ __('Kintai') }}{{ __('Details') }}{{ __('Report') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- 請求出力 --}}
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{ name_bill: '請求' }" x-text="name_bill" x-on:profile-updated.window="name_bill = $event.detail.name_bill"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('closebills', ['locale' => $locale])" :active="request()->routeIs($locale . '/closebills')" wire:navigate>
                            {{ __('Bill Close') }}
                            </x-dropdown-link>
                            <x-dropdown-link  :href="route('bills', ['locale' => $locale])" :active="request()->routeIs($locale . '/bills')" wire:navigate>
                            {{ __('Bill Export') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- 給与 --}}
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{ name_salary: '給与' }" x-text="name_salary" x-on:profile-updated.window="name_salary = $event.detail.name_salary"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('salaryemployee', ['locale' => $locale])" wire:navigate>
                                {{ __('Salary Deduct') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('closepayrolls', ['locale' => $locale])" :active="request()->routeIs($locale . '/closepayrolls')" wire:navigate>
                            {{ __('Salary Close') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('closesalaries', ['locale' => $locale])" wire:navigate>
                            {{ __('Salary Export') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- マスタ更新 --}}
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{ name: 'マスタ' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('holiday', ['locale' => $locale])" wire:navigate>
                                {{ __('Holiday') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('client', ['locale' => $locale])" wire:navigate>
                                {{ __('Client') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('clientplace', ['locale' => $locale])" wire:navigate>
                                {{ __('Work Place') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('clientworktype', ['locale' => $locale])" wire:navigate>
                                {{ __('Work Type') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('masterallowdeduct', ['locale' => $locale])" wire:navigate>
                                {{ __('Deduct Item') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('employee', ['locale' => $locale])" wire:navigate>
                                {{ __('Employee') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('user', ['locale' => $locale])" wire:navigate>
                                {{ __('Account') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- マニュアル --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('manual', ['locale' => $locale])" :active="request()->routeIs($locale . '/manual')" target="manual">
                        {{ __('Manual') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile', ['locale' => $locale])" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard', ['locale' => $locale])" :active="request()->routeIs($locale . '/dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        {{-- 
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('importkintai', ['locale' => $locale])" :active="request()->routeIs($locale . '/importkintai')" wire:navigate>
                {{ __('Import Kintai') }}
            </x-responsive-nav-link>
        </div>
        --}}

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile', ['locale' => $locale])" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
