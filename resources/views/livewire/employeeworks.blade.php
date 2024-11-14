<div>
<form>
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
        {{ $workYear }}年 {{ $workMonth }}月 {{ $Client['cl_name'] }} 様 {{ $ClientPlace['cl_pl_name'] }}
        【作業】
        @foreach($WorkTypes as $key => $value)
            @if($key != '')
            <span class="font-semibold text-green-500">{{ $key }}</span>:{{ $value }}
            @endif
        @endforeach
        <button wire:click.prevent="saveEmployeeWork" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold text-bold text-sm py-1 px-2 rounded" data-save="true">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table>
        {{--
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
        --}}
            <tbody>
            @foreach($TimekeepingDays as $key => $value)
            <tr>
                <td>{{ $value['day'] }}</td> 
                <td> 
                    {{-- 曜日 --}}
                    @if($this->rukuruUtilIsHoliday($client_id, $value['DateTime']->format('Y-m-d')))
                        <span style="color: red;">{{ $value['dispDayOfWeek'] }}</span>
                    @else
                        {{ $value['dispDayOfWeek'] }}
                    @endif
                </td>
                <td align="center">
                    {{-- 有給 --}}
                    <input type="checkbox" wire:model="TimekeepingDays.{{ $key }}.leave" wire:change="leaveChange($event.target.checked, {{$key}})" />
                </td>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <td>
                    {{-- 業務コード --}}
                    <input type="text" 
                        class="form-control px-1 py-1 text-xs" 
                        id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wt_cd" 
                        wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wt_cd" 
                        wire:change="workTypeChange($event.target.value, {{$key}}, {{$slotNo}})" 
                        style="width: 1.8rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wt_cd')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 業務名 --}}
                    <span class="text-xs">{{ $TimekeepingSlots[$key][$slotNo]['wt_name'] }}</span>
                </td>
                <td>
                    {{-- 開始打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 終了打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 3.4rem;" align="center">
                    {{-- 作業時間 --}}
                    <span class="form-control px-1 py-1 text-xs bg-gray-100" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_work_hours" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_work_hours">
                        {{ empty($TimekeepingSlots[$key][$slotNo]['wrk_work_hours']) ? '' : $TimekeepingSlots[$key][$slotNo]['wrk_work_hours'] }}
                    </span>
                </td>
                @endfor
            </tr>
            @endforeach
            </tbody>
        </table>
        <button wire:click.prevent="saveEmployeeWork" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold text-bold text-sm py-1 px-2 rounded" data-save="true">{{ __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
</form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
