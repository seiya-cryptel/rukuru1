<div>
<form>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" style="color: red;" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div class="col-md-8 py-1">
        {{ $workYear }}年 {{ $workMonth }}月
        <select class="form-control py-1 px-1 text-sm" 
            id="nextEmployeeId" 
            wire:model="nextEmployeeId" 
            wire:change="employeeChanged($event.target.value)"
            style="width: 16rem; padding: 0px;">
            @foreach($Employees as $key => $EmployeeRecord)
                <option value="{{ $EmployeeRecord->id }}">{{ $EmployeeRecord->empl_cd }} {{ $EmployeeRecord->empl_name_last }} {{ $EmployeeRecord->empl_name_first }}</option>
            @endforeach
        </select>
        {{ $Client['cl_name'] }} {{ $ClientPlace ? $ClientPlace['cl_pl_name'] : '' }}
        <button wire:click.prevent="saveEmployeeWork" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold text-bold text-sm py-1 px-2 rounded" data-save="true">{{ __('End') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table style="width: 100%;">
            <thead style="display: block;">
            <tr> {{-- 項目名 --}}
                <th style="width: 1rem;"> </th> {{-- 1 日付 }}
                <th style="width: 1rem;"> </th> {{-- 2 曜日 --}}
                <th style="width: 2rem;">有休</th> {{-- 3 有休 --}}
                <th class="text-center" style="width: 2.5rem; padding: 0px">勤務</th> {{-- 4 休日区分（勤務） --}}
                <th class="text-center" style="width: 8rem; padding: 0px">体系</th> {{-- 5 勤務体系 --}}
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">就業時間</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">普通残業</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">深夜時間</th>
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">深夜残業</th>
                <th style="width: 0.5rem; padding: 0px;"> </th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">就業</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">残業</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">深夜</th>
                <th style="width: 2.5rem; padding: 0px; text-align: center;">深残</th>
                <th style="width: 8rem; padding: 0px; text-align: center;">備考</th>
            </tr>
            </thead>

            <tbody style="display: block; height: 430px; overflow-y: auto;">
            @foreach($TimekeepingDays as $dayIndex => $value)
            <tr style="{{ $value['holiday_type'] == 1 ? 'color: blue; font-weight: bold;”' : ($value['holiday_type'] == 2 ? 'color: red; font-weight: bold;”' : '') }}">
                <td>{{ $value['day'] }}</td>  {{-- 1 日付 --}}
                <td> {{-- 2 曜日 --}}
                    @if($this->rukuruUtilIsHoliday($client_id, $value['DateTime']->format('Y-m-d')))
                        <span style="color: red;">{{ $value['dispDayOfWeek'] }}</span>
                    @else
                        {{ $value['dispDayOfWeek'] }}
                    @endif
                </td>
                <td> {{-- 3 有給 --}}
                    <input type="checkbox" 
                        {{-- tabindex="{{ $dayIndex }}01" --}}
                        wire:model="TimekeepingDays.{{ $dayIndex }}.leave" 
                        wire:change="leaveChange($event.target.checked, {{$dayIndex}})" 
                        />
                </td>
                <td> {{-- 4 休日区分 0: 平日, 1: 法定外休日, 2: 法定休日 --}}
                    <select 
                        {{-- tabindex="{{ $dayIndex }}02" --}}
                        class="form-control py-1 text-sm" 
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
                <td > {{-- 5 勤務体系 --}}
                    <select 
                        {{-- tabindex="{{ $dayIndex }}03" --}}
                        class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$dayIndex}}.work_type" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.work_type" 
                        wire:change="workTypeChange($event.target.value, {{$dayIndex}})"
                        style="width: 8rem; padding: 0px;">
                        @foreach($KinmuTaikeies as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('TimekeepingDays.'.$dayIndex.'.work_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                @for($slotNo = 1; $slotNo <= self::MAX_TIMESLOT; $slotNo++)
                <td style="width: 2.5rem; padding: 0px"> {{-- 就業時間 開始打刻 --}}
                    <input type="text" 
                        tabindex="{{ $dayIndex }}{{ $slotNo }}1"
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_start" 
                        wire:model.lazy="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_start" 
                        wire:change="logStartTimeChange($event.target.value, {{$dayIndex}}, {{$slotNo}})" 
                        style="width: 2.5rem; height: 22px; padding: 0px;{{($slotNo % 2) ? '' : ' background-color: #ffcc88;'}}" />
                    @error('TimekeepingSlots.'.$dayIndex.'.'.$slotNo.'.wrk_log_start')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 2.5rem; padding: 0px"> {{-- 就業時間 終了打刻 --}}
                    <input type="text" 
                        tabindex="{{ $dayIndex }}{{ $slotNo }}2"
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_end" 
                        wire:model.lazy="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_log_end" 
                        wire:change="logEndTimeChange($event.target.value, {{$dayIndex}}, {{$slotNo}})" 
                        style="width: 2.5rem; height: 22px; padding: 0px;{{($slotNo % 2) ? '' : ' background-color: #ffcc88;'}}" />
                    @error('TimekeepingSlots.'.$dayIndex.'.'.$slotNo.'.wrk_log_end')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                @endfor
                <td></td>
                @for($slotNo = 1; $slotNo <= self::MAX_TIMESLOT; $slotNo++)
                <td style="width: 2.5rem; padding: 0px;"> {{-- 就業時間 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs text-right" 
                        id="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_work_hours" 
                        wire:model="TimekeepingSlots.{{$dayIndex}}.{{$slotNo}}.wrk_work_hours" 
                        readonly="readonly"
                        style="width: 2.5rem; height: 22px; padding: 0px;{{($slotNo % 2) ? '' : ' background-color: #ffcc88;'}}" />
                </td>
                @endfor
                <td>
                    {{-- 備考 --}}
                    <input type="text" 
                        class="form-control py-1 text-xs" 
                        id="TimekeepingDays.{{$dayIndex}}.notes" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.notes"
                        style="width: 8rem; height: 22px; padding: 0px;" />
                    @error('TimekeepingDays.'.$dayIndex.'.notes')
                        <span class="text-red-500" style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
                <td>
                    <button 
                        wire:click.prevent="deleteTimekeepingDay({{ $dayIndex }})" 
                        class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm px-2 rounded">X</button>
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
                            <td class="form-control py-1" style="width: 2rem; text-align: center; background-color: #cc00cc; color: white;">有休<br>日数</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">就業<br>時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">普通<br>残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">深夜<br>時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">深夜<br>残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">法外<br>時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">法外<br>深夜</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">法定<br>時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color: #cc00cc; color: white;">法定<br>深夜</td>
                            <td> </td>
                            <td class="form-control py-1" style="width: 5rem; text-align: center; background-color: #cc00cc; color: white;">就業<br>時間計</td>
                        </tr>
                        <tr> {{-- 勤怠日数、時間数 --}}
                            <td style="text-align: center;">{{ $SumDaysShukkin }}</td>
                            <td style="text-align: center;">{{ $SumDaysKyujitsu }}</td>
                            <td style="text-align: center;">{{ $SumDaysHoutei }}</td>
                            <td style="text-align: center;">{{ $SumDaysYukyu }}</td>
                            <td> </td>
                            @for($slotNo = 1; $slotNo <= self::MAX_SUM_HOURSLOT; $slotNo++)
                                <td style="text-align: right;">{{ $SumWorkHours[$slotNo] }}</td>
                            @endfor
                            <td> </td>
                            <td style="text-align: right;">{{ $SumWorkHoursAll }}</td>
                        </tr>
                    </table>
                    <table class="border border-gray-300">
                        <tr> {{-- 支給 項目名 --}}
                            <td rowspan="2" class="form-control py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #aa8800;">支給</td>
                            <td style="width: 8rem;"></td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">普通給与</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">普通残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">深夜時間</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">深夜残業</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法外休出</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法外深夜</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法定休出</td>
                            <td class="form-control py-1" style="width: 4rem; text-align: center; background-color:#aa8800; color: white;">法定深夜</td>
                            <td></td>
                            <td class="form-control py-1" style="width: 5rem; text-align: center; background-color:#aa8800; color: white;">支給合計</td>
                        </tr>
                        <tr>
                            <td></td>
                            @for($slotNo = 1; $slotNo <= 8; $slotNo++)
                                <td style="text-align: right;">{{ number_format($SumWorkPays[$slotNo]) }}</td>
                            @endfor
                            <td></td>
                            <td style="text-align: right;">{{ number_format($SumWorkPayAll) }}</td>
                        </tr>

                        <tr> {{-- 請求 項目名 --}}
                            <td rowspan="2" class="form-control py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #0000aa;">請求</td>
                            <td></td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">普通給与</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">普通残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">深夜時間</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">深夜残業</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法外休出</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法外深夜</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法定休出</td>
                            <td class="form-control py-1" style="width: 3.5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">法定深夜</td>
                            <td></td>
                            <td class="form-control py-1" style="width: 5rem; padding: 0px; text-align: center; background-color: #0000aa; color: white;">請求合計</td>
                        </tr>
                        <tr>
                            <td></td>
                            @for($slotNo = 1; $slotNo <= 8; $slotNo++)
                                <td style="text-align: right;">{{ number_format($SumWorkBills[$slotNo]) }}</td>
                            @endfor
                            <td></td>
                            <td style="text-align: right;">{{ number_format($SumWorkBillAll) }}</td>
                        </tr>
                    </table>
                </td>
                <td> {{-- 単価表示 --}}
                    <table>
                    @for($i = 1; $i <= 8; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $i }}.wt_name" 
                                style="width: 8rem; padding: 0px; text-align: left; background-color:rgb(2, 66, 30); color: white;" />
                        </td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $i }}.wt_pay" 
                                style="width: 3rem; padding: 0px; text-align: right;" />
                        </td>
                        <td style="padding: 0px;">
                            <input type="text" 
                                readonly="readonly"
                                class="form-control text-sm"
                                wire:model="SumWorkTypes.{{ $i }}.wt_bill" 
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
