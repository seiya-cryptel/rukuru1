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
        {{ $workYear }}年 {{ $workMonth }}月 {{ $Client['cl_name'] }} 様 {{ empty($ClientPlace['cl_pl_name']) ? '' : $ClientPlace['cl_pl_name'] }}
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
            <tr> {{-- 項目名 --}}
                <td style="width: 1rem;"> </td>
                <td style="width: 1.2rem;"> </td>
                <th class="text-center" style="width: 2.5rem; padding: 0px">勤務</th>
                <th class="text-center" style="width: 3.5rem; padding: 0px">体系</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">就業時間</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">普通残業</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">深夜時間</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">深夜残業</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">就業</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">休出</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">普残</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">深夜</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">深残</th>
                <th style="width: 3.5rem; padding: 0px; text-align: center;">休暇</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">時間1</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">時間2</th>
                <th style="width: 8rem; padding: 0px; text-align: center;">備考</th>
            </tr>
            </thead>

            <tbody style="display: block; height: 430px; overflow-y: auto;">
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
                <td style="width: 2.5rem; padding: 0px"> {{-- 休日区分 0: 平日, 1: 法定外休日, 2: 法定休日 --}}
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$key}}.holiday_type" 
                        wire:model="TimekeepingDays.{{$key}}.holiday_type" 
                        wire:change="holidayTypeChange($event.target.value, {{$key}})"
                        style="width: 2.5rem; padding: 0px;">
                        <option value="0">平</option>
                        <option value="1">外</option>
                        <option value="2">法</option>
                    </select>
                    @error('TimekeepingDays.'.$key.'.holiday_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 3.5rem; padding: 0px"> {{-- 勤務体系 --}}
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$key}}.work_type" 
                        wire:model="TimekeepingDays.{{$key}}.work_type" 
                        wire:change="workTypeChange($event.target.value, {{$key}})"
                        style="width: 3.5rem; padding: 0px;">
                        @foreach($KinmuTaikeies as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('TimekeepingDays.'.$key.'.work_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>

                <td style="width: 2.5rem; padding: 0px"> {{-- 就業時間 開始打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_start" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$key}}, 'wrk_log_start')" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 就業時間 終了打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_end" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$key}}, 'wrk_log_start')" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 普通残業 開始打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_ovr_start" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_ovr_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$key}}, 'wrk_log_ovr_start')" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_ovr_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 普通残業 終了打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_ovr_end" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_ovr_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$key}}, 'wrk_log_ovr_end')" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_ovr_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 深夜 開始打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_midnight_start" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_midnight_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$key}}, 'wrk_log_midnight_start')" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_midnight_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 深夜 終了打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_midnight_end" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_midnight_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$key}}, 'wrk_log_midnight_end'" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_midnight_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 深夜残業 開始打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_midnight_ovr_start" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_midnight_ovr_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$key}}, 'wrk_log_midnight_ovr_start')" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_midnight_ovr_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 深夜残業 終了打刻 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_log_midnight_ovr_end" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_log_midnight_ovr_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$key}}, 'wrk_log_midnight_ovr_end')" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_ovr_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td></td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 就業時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_work_hours" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_work_hours" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 休出時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_work_hours_holiday" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_work_hours_holiday" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                </td>
                <td></td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 普通残業時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_work_ovr_hours" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_work_ovr_hours" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 深夜時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_work_midnight" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_work_midnight" 
                        style="width: 2.5rem; height: 22px; padding: 0px; background-color: #ffcc88;" />
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 深夜残業時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_work_ovr_midnight" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_work_ovr_midnight" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                </td>
                <td style="width: 3.5rem; padding: 0px"> {{-- 休暇 --}}
                    <select class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$key}}.holiday" 
                        wire:model="TimekeepingDays.{{$key}}.holiday" 
                        wire:change="holidayChange($event.target.value, {{$key}})"
                        style="width: 3.5rem; padding: 0px;">
                        <option value="1">有給</option>
                        <option value="2">特給</option>
                    </select>
                    @error('TimekeepingDays.'.$key.'.holiday') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 時間１ --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_hour1" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_hour1" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_hour1')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px;"> {{-- 時間2 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$key}}.wrk_hour2" 
                        wire:model="TimekeepingSlots.{{$key}}.wrk_hour2" 
                        style="width: 2.5rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_hour2')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    {{-- 備考 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs" 
                        id="TimekeepingSlots.{{$key}}.notes" 
                        wire:model="TimekeepingSlots.{{$key}}.notes"
                        style="width: 8rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingSlots.'.$key.'.wrk_log_end')
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
                            <td rowspan="2" class="form-control py-1" style="width: 3rem; padding: 0px; text-align: center; color: #ff00ff;">勤怠</td>
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">出勤<br>日数</td>
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">休日<br>出勤</td>
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">法定<br>出勤</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">就業<br>時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">普通<br>残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">深夜<br>時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">深夜<br>残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">法外<br>時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">法外<br>残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">法定<br>時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">法定<br>残業</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">就業<br>時間計</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">有休<br>日数</td>
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">特休<br>日数</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">有給<br>時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">夜間<br>有給</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #aa8800; color: white;">有給<br>金額</td>
                        </tr>
                        <tr> {{-- 勤怠日数、時間数 --}}
                            <td style="text-align: center;">{{ $SumDaysShukkin }}</td>
                            <td style="text-align: center;">{{ $SumDaysKyujitsu }}</td>
                            <td style="text-align: center;">{{ $SumDaysHoutei }}</td>
                            <td> </td>
                            @for($slotNo = 1; $slotNo <= 8; $slotNo++)
                                <td style="text-align: center;">{{ $SumWorkHours[$slotNo] }}</td>
                            @endfor
                            <td> </td>
                            <td style="text-align: center;">{{ $SumWorkHoursAll }}</td>
                            <td> </td>
                            <td style="text-align: center;">{{ $SumDaysYukyu }}</td>
                            <td style="text-align: center;">{{ $SumDaysTokkyu }}</td>
                            <td style="text-align: center;">{{ $SumWorkHoursYukyu }}</td>
                            <td style="text-align: center;">{{ $SumWorkHoursYukyuYakan }}</td>
                            <td> </td>
                            <td style="text-align: right;">{{ $SumWorksPayYukyu }}</td>
                        </tr>
                    </table>
                    <table class="border border-gray-300">
                        <tr> {{-- 支給 項目名 --}}
                            <td rowspan="2" class="form-control py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #aa8800;">支給</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">普通給与</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">普通残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">深夜時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">深夜残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法外休出</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法外深夜</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法定休出</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法定深夜</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">交通費</td>
                            <td style="width: 12rem;"></td>
                            <td class="form-control py-1" style="width: 5rem; text-align: center; background-color:#aa8800; color: white;">支給合計</td>
                        </tr>
                        <tr>
                            @for($slotNo = 1; $slotNo <= 8; $slotNo++)
                                <td style="text-align: right;">{{ $SumWorksPay[$slotNo] }}</td>
                            @endfor
                            <td style="text-align: right;">50,000</td>
                            <td></td>
                            <td style="text-align: right;">{{ $SumWorkPaysAll }}</td>
                        </tr>

                        <tr> {{-- 請求 項目名 --}}
                            <td rowspan="2" class="form-control py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #0000aa;">請求</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">普通給与</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">普通残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">深夜時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">深夜残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法外休出</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法外深夜</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法定休出</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法定深夜</td>
                            <td></td>
                            <td></td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">請求合計</td>
                        </tr>
                        <tr>
                            @for($slotNo = 1; $slotNo <= 8; $slotNo++)
                                <td style="text-align: right;">{{ $SumWorksBill[$slotNo] }}</td>
                            @endfor
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">{{ $SumWorkBillsAll }}</td>
                        </tr>
                    </table>
                </td>
                <td> {{-- 単価表示 --}}
                <table>
                    @for($slotNo = 1; $slotNo <= 8; $slotNo++)
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
                                wire:model="SumWorkTypes.{{ $slotNo }}.wt_pay" 
                                style="width: 3rem; padding: 0px; text-align: right;" />
                        </td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $slotNo }}.wt_bill" 
                                style="width: 3rem; padding: 0px; text-align: right;" />
                        </td>
                    </tr>
                    @endfor
                </table>
            </tr>
        </table>
    </div>
</form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
<script src="{{ asset('js/enter2tab.js') }}"></script>
