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
            <tr> {{-- 列名 --}}
                <th style="width: 1rem;"> </th>{{-- 1 日付 --}}
                <th style="width: 1.2rem;"> </th>{{-- 1 曜日 --}}
                <th style="width: 2rem;"> </th>{{-- 3 有給 --}}
                <th style="width: 3rem;"> </th>{{-- 4 休日区分（勤務） --}}
                <th style="width: 3.5rem;">体系</th>{{-- 5 勤務体系 --}}
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th colspan="2" style="width: 5rem; padding: 0px; text-align: center;">作業{{$slotNo}}</th>
                @endfor
                <th></th>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th style="width: 2.5rem; padding: 0px; text-align: center;"> </th>
                @endfor
                <th style="width: 8rem; padding: 0px; text-align: center;">備考</th>
                <th></th>
            </tr>
            <tr> {{-- 作業種別選択 --}}
                <th> </th>
                <th> </th>
                <th>有休</th>
                <th>勤務</th>
                <th>
                    <select class="form-control py-1 text-sm" 
                        id="HeaderWorkType" 
                        wire:model="HeaderWorkType" 
                        wire:change="workTypeChangeHeader($event.target.value)"
                        style="width: 3.5rem; padding: 0px;">
                        @foreach($KinmuTaikeies as $kinmuNo => $value)
                            <option value="{{ $kinmuNo }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </th>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th colspan="2" style="width: 5rem; padding: 0px;">
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
                </th>
                @endfor
                <th></th>
                @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                <th style="width: 2.5rem; padding: 0px; text-align: center;">{{$slotNo}}</th>
                @endfor
                <th style="width: 8rem; padding: 0px; text-align: center;"> </th>
                <th></th>
            </tr>
            </thead>

            <tbody style="display: block; height: 430px; overflow-y: auto;">
            @foreach($TimekeepingDays as $dayIndex => $value)
            <tr>
                <td style="text-align: right;">{{ $value['day'] }}</td> {{-- 1 日付 --}}
                <td> {{-- 曜日 --}}
                    @if($this->rukuruUtilIsHoliday($client_id, $value['DateTime']->format('Y-m-d')))
                        <span style="color: red;">{{ $value['dispDayOfWeek'] }}</span>
                    @else
                        {{ $value['dispDayOfWeek'] }}
                    @endif
                </td>
                <td style="width: 2rem;"> {{-- 有給 --}}
                    <input type="checkbox" 
                        {{-- tabindex="{{ $dayIndex }}01" --}}
                        wire:model="TimekeepingDays.{{ $dayIndex }}.leave" 
                        wire:change="leaveChange($event.target.checked, {{$dayIndex}})" 
                        />
                </td>
                <td style="width: 3rem; padding: 0px;"> {{-- [勤務] 休日区分 0: 平日, 1: 法定外休日, 2: 法定休日 --}}
                    <select 
                        {{-- tabindex="{{ $dayIndex }}02" --}}
                        class="form-control py-1 text-sm" 
                        id="TimekeepingDays.{{$dayIndex}}.holiday_type" 
                        wire:model="TimekeepingDays.{{$dayIndex}}.holiday_type" 
                        style="width: 3rem; padding: 0px;">
                        <option value="0">平</option>
                        <option value="1">外</option>
                        <option value="2">法</option>
                    </select>
                    @error('TimekeepingDays.'.$dayIndex.'.holiday_type') 
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </td>
                <td style="width: 3.5rem; padding: 0px;"> {{-- 勤務体系 1: 日勤, 2: 夜勤 --}}
                    <select 
                        {{-- tabindex="{{ $dayIndex }}03" --}}
                        class="form-control py-1 text-sm" 
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
                        tabindex="{{ $dayIndex }}{{ $slotNo }}1"
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
                        tabindex="{{ $dayIndex }}{{ $slotNo }}2"
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
                        readonly="readonly"
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
                <td>
                    <button 
                        wire:click.prevent="deleteTimekeepingDay({{ $dayIndex }})" 
                        class="bg-orange-500 hover:bg-orange-700 text-white font-semibold text-sm px-2 rounded">X</button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>

        <table style="width: 100%;"> {{-- 集計エリア --}}
        <tr>
            <td> {{-- 集計 --}}
                <table class="border border-gray-300">
                    <tr> {{-- 勤怠 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3rem; padding: 0px; text-align: center; color: #ff00ff;">勤怠</td>
                        <td class="form-control px-1 py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">出勤</td>
                        <td> </td>
                        <td class="form-control px-1 py-1" style="width: 3.5rem; text-align: center; background-color: #cc00cc; color: white;">有給</td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="width: 4rem; padding: 0px; text-align: center; background-color: #cc00cc; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="width: 6rem; padding: 0px; text-align: center; background-color: #cc00cc; color: white;">時間計</td>
                    </tr>
                    <tr> {{-- 勤怠日数、時間数 --}}
                    <td style="text-align: center;">{{ $SumDays }}</td>
                        <td> </td>
                        <td style="text-align: center;">{{ $SumDaysYukyu }}</td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="text-align: right;">{{ $SumWorkHours[$slotNo] }}</td>
                        @endfor
                        <td> </td>
                        <td style="text-align: right;">{{ $SumWorkHoursAll }}</td>
                    </tr>

                    <tr> {{-- 支給 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #aa8800;">支給</td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="text-align: center; background-color: #aa8800; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="text-align: center; background-color: #aa8800; color: white;">支給計</td>
                    </tr>
                    <tr> {{-- 支給 金額 --}}
                        <td style="text-align: right;"></td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="text-align: right;">{{ number_format($SumWorkPays[$slotNo]) }}</td>
                        @endfor
                        <td> </td>
                        <td style="text-align: right;">{{ number_format($SumWorkPayAll) }}</td>
                    </tr>

                    <tr> {{-- 請求 項目名 --}}
                        <td rowspan="2" class="form-control px-1 py-1" style="width: 3.3rem; padding: 0px; text-align: center; color: #0000aa;">請求</td>
                        <td class="form-control px-1 py-1"> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td class="form-control px-1 py-1" style="text-align: center; background-color: #0000aa; color: white;">作業{{ $slotNo }}</td>
                        @endfor
                        <td> </td>
                        <td style="text-align: center; background-color: #0000aa; color: white;">請求計</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"></td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        @for($slotNo = 1; $slotNo <= self::MAX_SLOTS; $slotNo++)
                            <td style="text-align: right;">{{ number_format($SumWorkBills[$slotNo]) }}</td>
                        @endfor
                        <td> </td>
                        <td style="text-align: right;">{{ number_format($SumWorkBillAll) }}</td>
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
