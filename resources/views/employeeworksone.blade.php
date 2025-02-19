<?php
use App\Models\employees as modelEmployees;

$Employee = modelEmployees::find($employeeId);
?>

<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight text-sm">
            {{ __('Kintai Entry') }} > {{ __('Employee') }} 
            <span class="font-semibold text-xl"> {{ $Employee->empl_cd }} {{ $Employee->empl_name_last }} {{ $Employee->empl_name_first }}</span> さん
        </h3>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="row justify-content-center mt-3">
                    @livewire('employeeworksone', ['workYear' => $workYear, 'workMonth' => $workMonth, 'client_id' => $clientId, 'clientplace_id' => $clientPlaceId, 'employee_id' => $employeeId]) 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
