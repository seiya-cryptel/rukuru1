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

    <div class="col-md-8"> {{-- 抽出条件 --}}
        <table class="py-1 text-sm">
        <tr>
            <td>
                勤怠期間
                <input type="date" 
                    class="form-control @error('dateFrom') is-invalid @enderror py-1 text-sm" id="workYear" 
                    wire:model="dateFrom" 
                    wire:change="changeDateFrom($event.target.value)" 
                    >
                @error('dateFrom') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                〜
                <input type="date" 
                    class="form-control @error('dateTo') is-invalid @enderror py-1 text-sm" id="workYear" 
                    wire:model="dateTo" 
                    wire:change="changeDateTo($event.target.value)" 
                    >
                @error('dateTo') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td> {{-- 顧客選択 --}}
                顧客
                <select 
                    class="form-control @error('client_id') is-invalid @enderror py-1 text-sm" 
                    wire:model="client_id" 
                    wire:change="changeClient($event.target.value)" 
                    style="width: 8rem;"
                    >
                    <option value="">全て</option>
                    @foreach ($Clients as $Client)
                        <option value="{{$Client->id}}">{{$Client->cl_name}}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                従業員
                <select 
                    class="form-control @error('emplCdFrom') is-invalid @enderror py-1 text-sm" 
                    wire:model="emplCdFrom" 
                    wire:change="changeEmployeeFrom($event.target.value)" 
                    style="width: 8rem;"
                    >
                    <option value=""></option>
                    @foreach ($Employees as $Employee)
                        <option value="{{$Employee->empl_cd}}">{{$Employee->empl_cd}} {{$Employee->empl_name_last}} {{$Employee->empl_name_first}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                〜
                <select 
                    class="form-control @error('emplCdTo') is-invalid @enderror py-1 text-sm" 
                    wire:model="emplCdTo" 
                    wire:change="changeEmployeeTo($event.target.value)" 
                    style="width: 8rem;"
                    >
                    <option value=""></option>
                    @foreach ($Employees as $Employee)
                        <option value="{{$Employee->empl_cd}}">{{$Employee->empl_cd}} {{$Employee->empl_name_last}} {{$Employee->empl_name_first}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <button wire:click.prevent="exportExcel()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Download') }}</span>
            </td>
        </tr>
        </table>
    </div>

    <div> {{-- 勤怠詳細一覧 --}}
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Paid Leave') }}</th>
                    <th>{{ __('Begin') }}</th>
                    <th>{{ __('End') }}</th>
                    <th>{{ __('Break') }}</th>
                    <th>{{ __('Hours') }}</th>
                    <th>{{ __('Item Name') }}</th>
                    <th>{{ __('Payout') }}</th>
                    <th>{{ __('Bill') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($Employeeworks as $Employeework)
                <tr class="border-b">
                    <td>
                        {{$Employeework->employee->empl_cd}} {{$Employeework->employee->empl_name_last}}{{$Employeework->employee->empl_name_first}}
                    </td>
                    <td>
                        {{$Employeework->wrk_date}}
                    </td>
                    <td>
                        {{$Employeework->wrk_seq}}
                    </td>
                    <td>
                        {{$Employeework->client->cl_name}}
                    </td>
                    <td>
                        {{$Employeework->leave == 1 ? '有休' : ($Employeework->leave == 2 ? '特休' : '')}}
                    </td>
                    <td>
                        {{$Employeework->wrk_log_start}}
                    </td>
                    <td>
                        {{$Employeework->wrk_log_end}}
                    </td>
                    <td>
                        {{$Employeework->wrk_break}}
                    </td>
                    <td>
                        {{$Employeework->wrk_work_hours}}
                    </td>
                    <td>
                        {{$Employeework->summary_name}}
                    </td>
                    <td style="text-align: right;">
                        {{number_format($Employeework->wrk_pay)}}
                    </td>
                    <td style="text-align: right;">
                        {{number_format($Employeework->wrk_bill)}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $Employeeworks->links() }}
    </div>
</div>
