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
        {{ $workYear }}年 {{ $workMonth }}月 {{ $Client['cl_cd'] }}:{{ $Client['cl_name'] }} 様 {{ $ClientPlace['cl_pl_cd'] }}:{{ $ClientPlace['cl_pl_name'] }} {{ $Employee['empl_cd'] }}:{{ $Employee['empl_name_last'] }} {{ $Employee['empl_name_first'] }} さん
        【業務】 
        @foreach($PossibleWorkTypes as $key => $value)
            {{ $key }}:{{ $value }}
        @endforeach

        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeeWork">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table>
            <thead>
            <tr>
                <th> </th>
                <th> </th>
                <th>休</th>
                @for($slotNo = 1; $slotNo <= 4; $slotNo++)
                <th colspan="4">{{ $slotNo }}</th>
                @endfor
            </tr>
            </thead>
            <tbody>
            @foreach($TimekeepingDays as $key => $value)
            <tr>
                <td>{{ $value['day'] }}</td> 
                <td> 
                    @if(in_array($value['dispDayOfWeek'], ['土', '日']))
                        <span style="color: red;">{{ $value['dispDayOfWeek'] }}</span>
                    @else
                        {{ $value['dispDayOfWeek'] }}
                    @endif
                </td>
                <td align="center">
                    <input type="checkbox" wire:model="TimekeepingDays.{{ $key }}.leave" />
                </td>
                @for($slotNo = 1; $slotNo <= 4; $slotNo++)
                <td>
                    <select class="form-control py-1 text-sm" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wt_cd" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wt_cd" wire:change="updateWorkType($event.target.value, {{$key}}, {{$slotNo}})">
                        <option value="N">{{ __('Normal') }}</option>
                        @foreach($PossibleWorkTypes as $keyType => $valueName)
                            @if($keyType != 'N')
                                <option value="{{ $keyType }}">{{ $valueName }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control py-1 text-sm" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" style="width: 4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control py-1 text-sm" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" style="width: 4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 4rem;" align="center">
                    <span class="form-control py-1 text-sm bg-gray-100" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_work_hours" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_work_hours">
                        {{ empty($TimekeepingSlots[$key][$slotNo]['wrk_work_hours']) ? '' : $TimekeepingSlots[$key][$slotNo]['wrk_work_hours'] }}
                    </span>
                </td>
                @endfor
            </tr>
            @endforeach
            </tbody>
        </table>
        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeeWork">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
    </div>
</div>
