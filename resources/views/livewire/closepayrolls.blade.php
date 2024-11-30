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
                @if($enableCloseButton)
                    @if($isClosed)
                       <button wire:click="reopenPayroll()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Reopen Payroll') }}</button>
                    @else
                        <button wire:click="closePayroll()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Close Payroll') }}</button>
                    @endif
                @else
                    <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded opacity-50 cursor-not-allowed" disabled>{{ __('Close Payroll') }}</button>
                @endif
            </td>
        </tr>
        </table>
    </div>
</div>
