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
                <input type="text" tabindex="1"
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
                <input type="text"  tabindex="2"
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
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th style="width: 4rem;">{{ __('Code') }}</th>
                    <th style="width: 24rem;">{{ __('Client') }}{{ __('Name') }}</th>
                    <th style="width: 4rem;">{{ __('Closing Date') }}</th>
                    <th style="width: 12rem;">{{ __('Kintai Period') }}</th>
                    <th style="width: 4rem;">{{ __('Number of People') }}</th>
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
                    <td class="text-right px-2">
                        {{$employeeCount[$Client->id]}}
                    </td>
                    <td>
                        @if($employeeCount[$Client->id])
                            @if($isClosed[$Client->id])
                                <button wire:click="reopenBill({{$Client->id}})" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Reopen Bill') }}</button>
                            @else
                                <button wire:click="closeBill({{$Client->id}})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Close Bill') }}</button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $Clients->links() }}

    <div>{{-- 勤怠エラー --}}
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th style="width: 12rem;">{{ __('Employee') }}</th>
                    <th style="width: 6rem;">{{ __('Date') }}</th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($KintaiErrors as $KintaiError)
                @php
                    $url = $KintaiError['url'];
                @endphp
                <tr class="border-b">
                    <td>
                        <a href="{{$url}}">{{$KintaiError['empl_cd']}} {{$KintaiError['empl_name']}}</a>
                    </td>
                    <td>
                        <a href="{{$url}}">{{$KintaiError['wrk_date']}}</a>
                    </td>
                    <td>
                        <a href="{{$url}}">{{$KintaiError['message']}}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{ asset('js/enter2tab.js') }}"></script>
