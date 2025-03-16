<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" style="color: blue;" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" style="color: red;" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="py-1 text-sm">
        <tr>
            <td>
                <input type="text" 
                    class="form-control @error('workYear') is-invalid @enderror py-1 text-sm" 
                    id="workYear" 
                    wire:model="workYear" 
                    wire:change="changeWorkYear($event.target.value)" 
                    style="width: 4rem;">
                @error('workYear') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                年
            </td>
            <td>
                <input type="text" 
                    class="form-control @error('workMonth') is-invalid @enderror py-1 text-sm" 
                    id="workMonth" 
                    wire:model="workMonth" 
                    wire:change="changeWorkMonth($event.target.value)" 
                    style="width: 3rem;">
                @error('workMonth') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                月
            </td>
            <td>
                <button wire:click="calcPayLeave()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Paid Leave Calc') }}</button>
            </td>
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th style="width: 12rem;">{{ __('Employee') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($Employees as $Employee)
                <tr class="border-b">
                    <td>
                        {{$Employee['empl_cd']}}
                        {{$Employee['empl_name']}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{ asset('js/enter2tab.js') }}"></script>
