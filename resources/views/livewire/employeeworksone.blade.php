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
        <button wire:click.prevent="selectAllowDeduct()" class="bg-green-500 hover:bg-green-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Salary Deduct') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table>
            <thead>
            <tr>
                <th> </th>
                <th> </th>
                <th>勤務</th>
                <th colspan="2">業務</th>
                <th colspan="2">就業</th>
                <th colspan="2">残業</th>
                <th colspan="2">深夜</th>
                <th colspan="2">深夜残業</th>
                <th>就業</th>
                <th>休出</th>
                <th>普残</th>
                <th>深夜</th>
                <th>深残</th>
                <th>休暇</th>
                <th>時間1</th>
                <th>時間2</th>
                <th>備考</th>
            </tr>
            </thead>
            <tbody>
            @php
                $slotNo = 1;
            @endphp
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
                    {{-- 就業 開始打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 就業 終了打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 普通残業 開始打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 普通残業 終了打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜 開始打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜 終了打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜残業 開始打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_start" wire:change="logStartTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜残業 終了打刻 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 就業時間 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 休出時間 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 普通残業時間 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜時間 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 深夜残業時間 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 休暇 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 時間１ --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 時間２ --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 備考 --}}
                    <input type="text" class="form-control px-1 py-1 text-xs {{ $TimekeepingSlots[$key][$slotNo]['class_bg_color'] }}" id="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:model="TimekeepingSlots.{{$key}}.{{$slotNo}}.wrk_log_end" wire:change="logEndTimeChange($event.target.value, {{$key}}, {{$slotNo}})" {{ $TimekeepingSlots[$key][$slotNo]['readonly'] }} style="width: 3.4rem;" />
                    @error('TimekeepingSlots.'.$key.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
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
<script src="{{ asset('js/enter2tab.js') }}"></script>
