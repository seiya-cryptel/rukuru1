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
        {{ $workYear }}年 {{ $workMonth }}月 {{ $Client['cl_name'] }} 様 {{ $ClientPlace ? $ClientPlace['cl_pl_name'] : '' }}
        {{--
        【作業】
        @foreach($WorkTypes as $key => $value)
            @if($key != '')
            <span class="font-semibold text-green-500">{{ $key }}</span>:{{ $value }}
            @endif
        @endforeach
        --}}
        <button wire:click.prevent="saveEmployeeWork" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold text-bold text-sm py-1 px-2 rounded" data-save="true">{{ __('Save') }}</button>
        {{-- <button wire:click.prevent="selectAllowDeduct()" class="bg-green-500 hover:bg-green-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Salary Deduct') }}</button> --}}
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table style="width: 100%;">
            <thead style="display: block;">
            <tr> {{-- 列名 --}}
                <th style="width: 1rem;"> </th>
                <th style="width: 1.2rem;"> </th>
                <td style="width: 1.2rem;"> </td>
                <td class="text-center" style="width: 2.5rem; padding: 0px;"> </td>
                <td class="text-center" style="width: 3.5rem; padding: 0px;"> </td>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">作業{{$slotNo}}</th>
                @endfor
                <th></th>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th style="width: 2.5rem; padding: 0px; text-align: center;"> </th>
                @endfor
                <th style="width: 8rem; padding: 0px; text-align: center;">備考</th>
            </tr>
            <tr> {{-- 作業種別選択 --}}
                <td style="width: 1rem;"> </td>
                <td style="width: 1.2rem;"> </td>
                <th style="width: 1.2rem;">休</th>
                <th class="text-center" style="width: 2.5rem; padding: 0px;">勤務</th>
                <th class="text-center" style="width: 3.5rem; padding: 0px;">体系</th>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <td colspan="2" style="width: 5rem; padding: 0px;">
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingTypes.{{$slotNo}}" 
                        wire:model="TimekeepingTypes.{{$slotNo}}" 
                        wire:change="workTypeChange($event.target.value, {{$slotNo}})" 
                        style="width: 5rem; padding: 0px;">
                        @foreach($WorkTypes as $wt_cd => $value)
                            <option value="{{ $wt_cd }}">{{ $wt_cd }} {{ $value }}</option>
                        @endforeach
                    </select>
                    @error('slot'.$slotNo.'_work_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                @endfor
                <td></td>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <td style="width: 2.5rem; padding: 0px; text-align: center;">{{$slotNo}}</td>
                @endfor
                <td style="width: 8rem; padding: 0px; text-align: center;"> </td>
            </tr>
            </thead>

            <tbody style="display: block; height: 430px; overflow-y: auto;">
            @foreach($TimekeepingDays as $dayIndex => $value)
            <tr>
                <td>{{ $value['day'] }}</td> 
                <td> {{-- 曜日 --}}
                    @if($this->rukuruUtilIsHoliday($client_id, $value['DateTime']->format('Y-m-d')))
                        <span style="color: red;">{{ $value['dispDayOfWeek'] }}</span>
                    @else
                        {{ $value['dispDayOfWeek'] }}
                    @endif
                </td>
                <td> {{-- 有給 --}}
                    <input type="checkbox" 
                        wire:model="TimekeepingDays.{{ $dayIndex }}.leave" 
                        wire:change="leaveChange($event.target.checked, {{$dayIndex}})" />
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 休日区分 0: 平日, 1: 法定外休日, 2: 法定休日 --}}
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$dayIndex}}.holiday_type" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.holiday_type" 
                        wire:change="holidayTypeChange($event.target.value, {{$dayIndex}})"
                        style="width: 2.5rem; padding: 0px;">
                        <option value="0">平</option>
                        <option value="1">外</option>
                        <option value="2">法</option>
                    </select>
                    @error('TimekeepingDays.'.$dayIndex.'.holiday_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 3.5rem; padding: 0px;"> {{-- 勤務体系 1: 日勤, 2: 夜勤 --}}
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$dayIndex}}.work_type" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.work_type" 
                        wire:change="workTypeChange($event.target.value, {{$dayIndex}})"
                        style="width: 3.5rem; padding: 0px;">
                        @foreach($KinmuTaikeies as $kinmuNo => $value)
                            <option value="{{ $kinmuNo }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('TimekeepingDays.'.$dayIndex.'.work_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <td style="width: 2.5rem; padding: 0px;"> {{-- 勤務 開始打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_start" 
                        wire:model="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$dayIndex}}, {{$slotNo}})" 
                        style="width: 2.5rem; height: 22px; padding: 0px;{{ $SlotBGColors[$slotNo] ? ' background-color: #ffcc88;' : '' }}" />
                    @error('TimekeepingSlots.'.$dayIndex.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 勤務 終了打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_end" 
                        wire:model="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$dayIndex}}, {{$slotNo}})" 
                        style="width: 2.5rem; height: 22px; padding: 0px;{{ $SlotBGColors[$slotNo] ? ' background-color: #ffcc88;' : '' }}" />
                    @error('TimekeepingSlots.'.$dayIndex.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                @endfor
                <td></td>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <td style="width: 2.5rem; padding: 0px;">
                    {{-- 時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_work_hours" 
                        wire:model="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_work_hours" 
                        style="width: 2.5rem; height: 22px; padding: 0px;{{ $SlotBGColors[$slotNo] ? ' background-color: #ffcc88;' : '' }}" />
                    @error('TimekeepingSlots.'.$dayIndex.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                @endfor
                <td>
                    {{-- 備考 --}}
                    <input type="text" 
                        class="form-control px-1 py-1 text-xs bg-orange-100" 
                        id="TimekeepingDays.{{$dayIndex}}.notes" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.notes" 
                        style="width: 8rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingDays.'.$dayIndex.'.notes')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <table style="width: 100%;">
        <tr>
            <td> {{-- 集計 --}}
                <table class="border border-gray-300">
                    <tr> {{-- 勤怠 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3rem; padding: 0px; text-align: center; color: #ff00ff;">勤怠</td>
                        <td class="form-control px-1 py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">出勤</td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #cc00cc; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 4rem; padding: 0px; text-align: center; background-color: #cc00cc; color: white;">時間計</td>
                    </tr>
                    <tr> {{-- 勤怠日数、時間数 --}}
                        <td style="text-align: center;">{{ $SumDays }}</td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ $SumWorkHours[$slotNo] }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ $SumWorkHoursAll }}</td>
                    </tr>

                    <tr> {{-- 支給 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #aa8800;">支給</td>
                        <td class="form-control px-1 py-1" style="text-align: center; background-color:#aa8800; color: white;">交通費</td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #aa8800; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 3.5rem; padding: 0px; text-align: center; background-color: #aa8800; color: white;">支給計</td>
                    </tr>
                    <tr> {{-- 支給 金額 --}}
                        <td style="text-align: right;"></td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ number_format($SumWorkPays[$slotNo]) }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ number_format($SumWorkPayAll) }}</td>
                    </tr>

                    <tr> {{-- 請求 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #0000aa;">請求</td>
                        <td class="form-control px-1 py-1"> </td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">請求計</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"></td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ number_format($SumWorkBills[$slotNo]) }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 3.5rem; padding: 0px; text-align: right;">{{ number_format($SumWorkBillAll) }}</td>
                    </tr>
                </table>
            </td>
            <td> {{-- 単価表示 --}}
                <table>
                    @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                    <tr>
                        <td>{{ $slotNo }}</td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $slotNo }}.wt_name" 
                                style="width: 8rem; padding: 0px; text-align: left; background-color:rgb(2, 66, 30); color: white;" />
                        </td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $slotNo }}.wt_pay_std" 
                                style="width: 3rem; padding: 0px; text-align: right;" />
                        </td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $slotNo }}.wt_bill_std" 
                                style="width: 3rem; padding: 0px; text-align: right;" />
                        </td>
                    </tr>
                    @endfor
                </table>
            </td>
        </tr>
        </table>
    </div>
</form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
<script src="{{ asset('js/enter2tab.js') }}"></script>
