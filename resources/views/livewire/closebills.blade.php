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
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th style="width: 4rem;">{{ __('Code') }}</th>
                    <th>{{ __('Client') }}{{ __('Name') }}</th>
                    <th>{{ __('Closing Date') }}</th>
                    <th>{{ __('Kintai Period') }}</th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($Clients as $Client)
                <tr class="border-b">
                    <td>
                        {{$Client->cl_cd}}
                    </td>
                    <td>
                        {{$Client->cl_name}}
                    </td>
                    <td>
                        {{$Client->cl_close_day ? $Client->cl_close_day : '末日'}}
                    </td>
                    <td>
                        {{$periods[$Client->id]}}
                    </td>
                    <td>
                        @if($isClosed[$Client->id])
                            <button wire:click="reopenPayroll({{$Client->id}})" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Reopen Bill') }}</button>
                        @else
                            <button wire:click="closePayroll({{$Client->id}})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Close Bill') }}</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
