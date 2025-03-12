<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="py-1 text-sm">
        <tr>
            <td>
                <input type="text" class="form-control @error('workYear') is-invalid @enderror py-1 text-sm" id="workYear" wire:model="workYear" wire:change="changeWorkYear($event.target.value)" style="width: 4rem;">
                @error('workYear') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                年
            </td>
            <td>
                <input type="text" class="form-control @error('workMonth') is-invalid @enderror py-1 text-sm" id="workMonth" wire:model="workMonth" wire:change="changeWorkMonth($event.target.value)" style="width: 3rem;">
                @error('workMonth') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                月
            </td>
            <td>
                @if($isClosed)
                    <button wire:click="reopenPayroll()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Reopen Payroll') }}</button>
                @else
                    <button wire:click="closePayroll()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Close Payroll') }}</button>
                @endif
            </td>
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th style="width: 4rem;">{{ __('Code') }}</th>
                    <th style="width: 12rem;">{{ __('Employee') }}{{ __('Name') }}</th>
                    <th style="width: 8rem;">{{ __('勤怠支給額') }}</th>
                    <th style="width: 8rem;">{{ __('交通費') }}</th>
                    <th style="width: 8rem;">{{ __('手当') }}</th>
                    <th style="width: 8rem;">{{ __('控除') }}</th>
                    <th style="width: 8rem;">{{ __('総支給額') }}</th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($Employees as $Employee)
                <tr class="border-b">
                    <td>
                        {{$Employee['empl_cd']}}
                    </td>
                    <td>
                        {{$Employee['empl_name']}}
                    </td>
                    <td class="text-right px-2">
                        {{number_format($Employee['work_amount'])}}
                    </td>
                    <td class="text-right px-2">
                        {{number_format($Employee['transport'])}}
                    </td>
                    <td class="text-right px-2">
                        {{number_format($Employee['allow_amount'])}}
                    </td>
                    <td class="text-right px-2">
                        {{number_format($Employee['deduct_amount'])}}
                    </td>
                    <td class="text-right px-2">
                        {{number_format($Employee['pay_amount'])}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
