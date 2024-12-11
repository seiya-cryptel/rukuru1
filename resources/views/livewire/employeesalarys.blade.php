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
    <div class="col-md-8 py-1">
        {{ $workYear }}年 {{ $workMonth }}月 {{ $Employee->empl_cd }}:{{ $Employee->empl_name_last }} {{ $Employee->empl_name_first }} さん

        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeeSalary" data-save="true">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeeSalary()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table>
        <tr>
        @for($i = 0; $i < 10; $i++)
            @if($i == 0)
            <th rowspan="2" style="color: blue; width: 4rem;">手当</th>
            @endif
            <td>
                <select class="form-control py-1 text-sm" id="Allows.{{$i}}.id" wire:model="Allows.{{$i}}.id" style="width: 8rem;">
                <option value=""></option>
                @foreach($refAllows as $refAllow)
                    <option value="{{ $refAllow->id }}">{{ $refAllow->mad_name }}</option>
                @endforeach
                </select>

                <input type="text" class="form-control py-1 text-sm text-right" id="Allows.{{$i}}.amount" wire:model="Allows.{{$i}}.amount" wire:change="moneyChange($event.target.value, 'Allows', {{$i}})" style="width: 5rem;" />
                @error('Allows.'.$i.'.amount')
                    <span class="text-red-500" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            @if($i == 4)
            </tr><tr>
            @endif
        @endfor
        </tr>
        <tr>
        @for($i = 0; $i < 10; $i++)
            @if($i == 0)
            <th rowspan="2" style="color: darkred;">控除</th>
            @endif
            <td>
                <select class="form-control py-1 text-sm" id="Deducts.{{$i}}.id" wire:model="Deducts.{{$i}}.id" style="width: 8rem;">
                <option value=""></option>
                @foreach($refDeducts as $refDeduct)
                    <option value="{{ $refDeduct->id }}">{{ $refDeduct->mad_name }}</option>
                @endforeach
                </select>

                <input type="text" class="form-control py-1 text-sm text-right" id="Deducts.{{$i}}.amount" wire:model="Deducts.{{$i}}.amount" wire:change="moneyChange($event.target.value, 'Deducts', {{$i}})" style="width: 5rem;" />
                @error('Deducts.'.$i.'.amount')
                    <span class="text-red-500" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            @if($i == 4)
            </tr><tr>
            @endif
        @endfor
        </tr>            
        </table>
        <table>
        <tr>
            <td style="width: 4rem; font-weight: bold; text-align: right;">+交通費</td>
            <td>
                <input type="text" class="form-control py-1 text-sm text-right" id="Transport" wire:model="Transport" wire:change="transportChange($event.target.value)" style="width: 5rem;" />
                @error('Transport')
                    <span class="text-red-500" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            <td style="width: 5rem; font-weight: bold; text-align: right;">+手当計</td>
            <td>
                <input type="text" class="form-control py-1 text-sm text-right" id="TotalAllow" wire:model="TotalAllow" style="width: 5rem; background-color: lightblue;" readonly="readonly" />
            </td>
            <td style="width: 4rem; font-weight: bold; text-align: right;">-控除計</td>
            <td>
                <input type="text" class="form-control py-1 text-sm text-right" id="TotalDeduct" wire:model="TotalDeduct" style="width: 5rem; background-color: orange;"  readonly="readonly" />
            </td>
            <td style="width: 4rem; font-weight: bold; text-align: right;">+給与計</td>
            <td>
                <input type="text" class="form-control py-1 text-sm text-right" id="TotalPay" wire:model="TotalPay" style="width: 5rem; background-color: lightblue;"  readonly="readonly" />
            </td>
            <td style="width: 4rem; font-weight: bold; text-align: right;">=支給額</td>
            <td>
                <input type="text" class="form-control py-1 text-sm text-right" id="PayAmount" wire:model="PayAmount" style="width: 5rem; background-color: yellow;" />
            </td>
        </tr>
        </table>
        <table>
        <tr>
            <th>日付</th>
            <th>顧客</th>
            <th>場所</th>
            <th colspan="2">作業</th>
            <th style="width: 4rem;">開始</th>
            <th style="width: 4rem;">終了</th>
            <th style="width: 4rem;">時間</th>
            <th style="width: 4rem;">時給</th>
            <th style="width: 4rem;">支給</th>
        </tr>
        @foreach($refEmployeeSalarys as $refEmployeeSalary)
        <tr>
            <td>{{ $refEmployeeSalary->wrk_date }}</td> 
            <td>{{ $refEmployeeSalary->client->cl_name }}</td> 
            <td>{{ $refEmployeeSalary->clientplace->cl_pl_name }}</td> 
            <td>{{ $refEmployeeSalary->wt_cd }}</td> 
            <td></td>
            <td class="text-center">{{ $refEmployeeSalary->wrk_work_start }}</td> 
            <td class="text-center">{{ $refEmployeeSalary->wrk_work_end }}</td> 
            <td class="text-right">{{ $refEmployeeSalary->wrk_work_hours }}</td> 
            <td class="text-right">{{ number_format($refEmployeeSalary->payhour) }}</td> 
            <td class="text-right">{{ number_format($refEmployeeSalary->wrk_pay) }}</td> 
        </tr>
        @endforeach
        </table>
        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeeSalary" data-save="true">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeeSalary()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
</div>
