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
            <th rowspan="2" style="color: blue;">手当</th>
            @endif
            <td>
                <select class="form-control py-1 text-sm" id="Allows.{{$i}}.mad_cd" wire:model="Allows.{{$i}}.mad_cd" style="width: 6rem;">
                <option value=""></option>
                @foreach($refAllows as $refAllow)
                    <option value="{{ $refAllow->mad_cd }}">{{ $refAllow->mad_name }}</option>
                @endforeach
                </select>

                <input type="text" class="form-control py-1 text-sm" id="Allows.{{$i}}.mad_amount" wire:model="Allows.{{$i}}.mad_amount" style="width: 5rem;" />
                @error('Allows.'.$i.'.mad_amount')
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
                <select class="form-control py-1 text-sm" id="Deducts.{{$i}}.mad_cd" wire:model="Deducts.{{$i}}.mad_cd" style="width: 6rem;">
                <option value=""></option>
                @foreach($refDeducts as $refDeduct)
                    <option value="{{ $refDeduct->mad_cd }}">{{ $refDeduct->mad_name }}</option>
                @endforeach
                </select>

                <input type="text" class="form-control py-1 text-sm" id="Deducts.{{$i}}.mad_amount" wire:model="Deducts.{{$i}}.mad_amount" style="width: 5rem;" />
                @error('Deducts.'.$i.'.mad_amount')
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
            <td>
                交通費
                <input type="text" class="form-control py-1 text-sm" id="Transport" wire:model="Transport" style="width: 5rem;" />
                @error('Transport')
                    <span class="text-red-500" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        </table>
        <table>
        <tr>
            <th>日付</th>
            <th>顧客</th>
            <th>場所</th>
            <th>勤務</th>
            <th>開始</th>
            <th>終了</th>
            <th>時間</th>
            <th>時給</th>
            <th>割増</th>
            <th>支給</th>
        </tr>
        @foreach($refEmployeeSalarys as $refEmployeeSalary)
        <tr>
            <td>{{ $refEmployeeSalary->wrk_date }}</td> 
            <td>{{ $refEmployeeSalary->client->cl_name }}</td> 
            <td>{{ $refEmployeeSalary->clientplace->cl_pl_name }}</td> 
            <td>{{ $refEmployeeSalary->wt_cd }}</td> 
            <td>{{ $refEmployeeSalary->wrk_work_start }}</td> 
            <td>{{ $refEmployeeSalary->wrk_work_end }}</td> 
            <td>{{ $refEmployeeSalary->wrk_work_hours }}</td> 
            <td>{{ $refEmployeeSalary->payhour }}</td> 
            <td>{{ $refEmployeeSalary->premiun }}</td> 
            <td>{{ $refEmployeeSalary->wrk_pay }}</td> 
        </tr>
        @endforeach
        </table>
        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeeSalary" data-save="true">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeeSalary()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
</div>
