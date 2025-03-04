<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;
use App\Services\TimeSlotOne;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;
use App\Models\worktype as modelWorktypes;
use App\Models\employees as modelEmployees;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeesalarys as modelEmployeeSalarys;
use App\Models\salary as modelSalary;

use App\Services\WorkhoursType1;

/**
 * 勤怠入力画面標準
 */
class Employeeworksone extends EmployeeworksBase
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants
    private const MAX_TIMESLOT = 4;
    private const MAX_HOURSLOT = 5;
    private const MAX_SUM_HOURSLOT = 8; // 時間集計用スロット数

    /**
     * possible work types
     */
    public $PossibleWorkTypeFirst;

    /**
     * 勤務体系
     */
    public $WorktypeRecords;

    /**
     * スロットの背景色
     */
    public $SlotBGColors = [];

    // 計算用
                                    // 0 作業なし, 1 作業あり
    public $DayHasWorkShukkin = [];        // 出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkKyujitsu = [];        // 法定外休日出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkHoutei = [];        // 法定休日出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkYukyu = [];        // 有給休暇日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkTokkyu = [];        // 特別休暇日数 [$dayIndex] ごとの作業の有無

    /**
     * 集計表示用
     */
    public $SumDaysShukkin;            // 日数 出勤
    public $SumDaysKyujitsu;           // 日数 法定外休日
    public $SumDaysHoutei;             // 日数 法定休日
    public $SumDaysYukyu;              // 日数 有給
    public $SumDaysTokkyu;            // 日数 特休

    public $SumWorksPayYukyu = 0;   //  1ヶ月の有給支給額の合計

    /**
     * validation rules
     */
    protected $rules = [
    ];

    /**
     * 勤怠タイプ
     */
    protected function worktypeKintai() : int
    {
        return 0;
    }

    /**
     * 集計エリアをクリアする
     */
    protected function clearSummary()
    {
        $this->SumDaysShukkin = 0;            // 平日出勤日数
        $this->SumDaysKyujitsu = 0;            // 休日出勤日数
        $this->SumDaysHoutei = 0;       // 法定休日出勤日数
        $this->SumDaysYukyu = 0;            // 有給休暇日数
        $this->SumDaysTokkyu = 0;            // 特別休暇日数
        
        $this->SumWorkHours = array_fill(1, 10, '00:00');  // 作業時間合計
        $this->SumWorkHoursAll = '00:00';  // 1ヶ月の作業時間合計
        $this->SumWorkHoursYukyu = '00:00';  // 1ヶ月の有給時間合計
        $this->SumWorkHoursYukyuYakan = '00:00';  // 1ヶ月の夜間有給時間合計

        $this->SumWorkPays = array_fill(1, 8, 0);  // 支給額合計
        $this->SumWorksPayYukyu = 0;   // 1ヶ月の有給支給額合計
        $this->SumWorkPayAll = 0;   // 1ヶ月の支給額合計

        $this->SumWorkBills = array_fill(1, 8, 0);  // 請求額合計
        $this->SumWorkBillAll = 0;   // 1ヶ月の請求額合計

        // 作業種別名、時給、請求額をクリア
        $this->SumWorkTypes=[
            1 => ['wt_name' => '基本', 'wt_pay' => 0, 'wt_bill' => 0],
            2 => ['wt_name' => '普通残業', 'wt_pay' => 0, 'wt_bill' => 0],
            3 => ['wt_name' => '深夜時間', 'wt_pay' => 0, 'wt_bill' => 0],
            4 => ['wt_name' => '深夜残業', 'wt_pay' => 0, 'wt_bill' => 0],
            5 => ['wt_name' => '法外休出', 'wt_pay' => 0, 'wt_bill' => 0],
            6 => ['wt_name' => '法外深夜', 'wt_pay' => 0, 'wt_bill' => 0],
            7 => ['wt_name' => '法定休出', 'wt_pay' => 0, 'wt_bill' => 0],
            8 => ['wt_name' => '法定深夜', 'wt_pay' => 0, 'wt_bill' => 0],
        ];
    }

    /**
     * 更新スロット番号から時間を表示するスロットを計算する
     * @param $slotNo スロット番号
     * @param $holiday_type 休日区分
     * @return int 時間スロット番号
     */
    protected function calcSlotHour($slotNo, $holiday_type)
    {
        if($slotNo == 1)  // 就業時間
        {
            $slotHour = $holiday_type == 0 ? 1 : 2;    // 就業または休出に時間数表示
        }
        else{
            $slotHour = $slotNo + 1;
        }
        return $slotHour;
    }

    /**
     * 更新スロット番号から時間を集計するスロットを計算する
     * @param $slotNo スロット番号
     * @param $holiday_type 休日区分
     * @return int 集計スロット番号
     */
    protected function calcSlotSum($slotNo, $holiday_type)
    {
        switch($slotNo)
        {
            case 1: // 就業時間
                switch($holiday_type)  // 勤務
                {
                    case 1: // 法定外休日
                        $slotSum = 5;
                        break;
                    case 2: // 法定休日
                        $slotSum = 7;
                        break;
                    default:
                        $slotSum = 1;
                        break;
                }
                break;
            case 2: // 普通残業
                $slotSum = 2;
                break;
            case 3: // 深夜時間
                switch($holiday_type)  // 勤務
                {
                    case 1: // 法定外休日
                        $slotSum = 6;
                        break;
                    case 2: // 法定休日
                        $slotSum = 8;
                        break;
                    default:
                        $slotSum = 3;
                        break;
                }
                break;
            case 4: // 深夜残業
                $slotSum = 4;
                break;
            default:
                break;
        }
        return $slotSum;
    }

    /**
     * 1ヶ月作業時間合計を計算
     * @return dateInterval 作業時間合計
     */
    protected function calcMonthWorkHours()
    {
        // 1ヶ月の作業時間合計 有給含む クリア
        $diSumWorkHours = [];
        $diZero = new DateInterval('P0D');  // DateInterval 就業時間
        for($slotSum = 1; $slotSum <= self::MAX_SUM_HOURSLOT + 2; $slotSum++)
        {
            $diSumWorkHours[$slotSum] = clone $diZero;
        }

        // 1ヶ月の作業時間合計を計算
        $i = 1;
        foreach($this->TimekeepingDays as $day => $Day)
        {
            $holiday_type = $this->TimekeepingDays[$day]['holiday_type'];

            // 入力スロットごとに
            for($slotNo = 1; $slotNo <= self::MAX_TIMESLOT; $slotNo++)
            {
                // 時間スロット番号を計算する
                $slotHour = $this->calcSlotHour($slotNo, $holiday_type);
                // 集計スロット番号を計算する
                $slotSum = $this->calcSlotSum($slotNo, $holiday_type);

                switch($holiday_type)
                {
                    case 1: // 法定外休日
                    case 2: // 法定休日
                        switch($slotNo)
                        {
                            case 1: // 就業
                                $hhmmHour = $this->TimekeepingSlots[$day][$slotHour]['wrk_work_hours']; // 時間 hh:mm
                                if(!empty($hhmmHour))
                                {
                                    $diHour1 = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                                    $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour1);
                                }
                                // 普通残業
                                $hhmmHour = $this->TimekeepingSlots[$day][$slotHour + 1]['wrk_work_hours']; // 時間 hh:mm
                                if(!empty($hhmmHour))
                                {
                                    $diHour1 = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                                    $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour1);
                                }
                                break;
                            case 3: // 深夜時間
                                $hhmmHour = $this->TimekeepingSlots[$day][$slotHour]['wrk_work_hours']; // 時間 hh:mm
                                if(!empty($hhmmHour))
                                {
                                    $diHour1 = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                                    $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour1);
                                }
                                // 深夜残業
                                $hhmmHour = $this->TimekeepingSlots[$day][$slotHour + 1]['wrk_work_hours']; // 時間 hh:mm
                                if(!empty($hhmmHour))
                                {
                                    $diHour1 = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                                    $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour1);
                                }
                                break;
                            default:
                                break;
                        }
                        break;
                    default:    // 平日
                        $hhmmHour = empty($this->TimekeepingSlots[$day][$slotHour]['wrk_work_hours']) ? 
                            '00:00' : $this->TimekeepingSlots[$day][$slotHour]['wrk_work_hours']; // 時間 hh:mm
                        if(!empty($hhmmHour))
                        {
                            $diHour1 = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                            $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour1);
                        }
                        break;
                }
            }
            // 有給時間
            $hhmmHour = $this->TimekeepingDays[$day]['wrk_leave_hour1']; // 時間 hh:mm
            if(!empty($hhmmHour))
            {
                $diHour = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                $diSumWorkHours[9] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[9], $diHour);
            }
            // 夜間有給
            $hhmmHour = $this->TimekeepingDays[$day]['wrk_leave_hour2']; // 時間 hh:mm
            if(!empty($hhmmHour))
            {
                $diHour = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                $diSumWorkHours[10] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[10], $diHour);
            }
            $i++;
        }

        for($i = 1; $i <= self::MAX_SUM_HOURSLOT + 2; $i++)
        {
            $this->SumWorkHours[$i] = $this->rukuruUtilDateIntervalFormat($diSumWorkHours[$i]);
        }
        return;
    }

    /**
     * 1つのスロットの時間が変更されたら以下を再計算する
     * 1. 作業日数合計
     * 2. スロットの作業時間合計
     * 3. スロットの支給額合計
     * 4. スロットの請求額合計
     * @param $day 日
     * @param $slot スロット
     */
    protected function calcSlot($day, $slot)
    {
        // 1 日の作業時間合計を計算
        $dayWorkHours = $this->sumDayWorkHours($day, self::MAX_HOURSLOT);  // DateInterval object
        $hhmmDayWorkHours = $this->rukuruUtilDateIntervalFormat($dayWorkHours);

        // 勤務区分 0: 平日, 1: 法定外休日, 2: 法定休日）
        $holiday_type = $this->TimekeepingDays[$day]['holiday_type'];

        // 作業日数合計
        $presence = ($hhmmDayWorkHours == '00:00') ? 0 : 1;
        switch($holiday_type)
        {
            case 0: // 平日
                $this->DayHasWorkShukkin[$day] = $presence;
                break;
            case 1: // 法定外休日
                $this->DayHasWorkKyujitsu[$day] = $presence;
                break;
            case 2: // 法定休日
                $this->DayHasWorkHoutei[$day] = $presence;
                break;
            default:
                break;
        }
        $this->SumDaysShukkin = array_sum($this->DayHasWorkShukkin);
        $this->SumDaysKyujitsu = array_sum($this->DayHasWorkKyujitsu);
        $this->SumDaysHoutei = array_sum($this->DayHasWorkHoutei);

        // 休暇 1: 有給, 2: 特急
        $leave = $this->TimekeepingDays[$day]['leave'];

        switch($leave)
        {
            case 1: // 有給
                $hhmmWorkHour = $this->TimekeepingDays[$day]['wrk_leave_hour1']; // 時間 hh:mm
                $dayWorkHours = $this->rukuruUtilTimeToDateInterval($hhmmWorkHour);
                $hhmmWorkHour = $this->rukuruUtilDateIntervalFormat($dayWorkHours);
                if($hhmmWorkHour != '00:00')
                {
                    $this->DayHasWorkYukyu[$day] = 1;
                }
                break;
            case 2: // 特急
                $hhmmWorkHour = $this->TimekeepingSlots[$day]['wrk_leave_hour2']; // 時間 hh:mm
                $dayWorkHours = $this->rukuruUtilTimeToDateInterval($hhmmWorkHour);
                $hhmmWorkHour = $this->rukuruUtilDateIntervalFormat($dayWorkHours);
                if($hhmmWorkHour != '00:00')
                {
                    $this->DayHasWorkTokkyu[$day] = 1;
                }
                break;
            default:
                break;
        }
        $this->SumDaysYukyu = array_sum($this->DayHasWorkYukyu);
        $this->SumDaysTokkyu = array_sum($this->DayHasWorkTokkyu);

        // 時間スロットの1ヶ月の作業時間合計
        $diWorkHours = $this->calcMonthWorkHours();

        // 有給金額の計算
        $di = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[9]);
        $unit_price = $this->rukuruUtilMoneyValue($this->Employee->empl_paid_leave_pay);
        $pay = $this->rukuruUtilDateIntervalToMoney($di, $unit_price);
        $di = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[10]);
        $unit_price = $this->rukuruUtilMoneyValue($this->Employee->empl_paid_leave_pay);  // ? 夜間有給金額単価は？
        $pay += $this->rukuruUtilDateIntervalToMoney($di, $unit_price);
        $this->SumWorksPayYukyu = $pay;
        // 支給合計
        $payAll = $pay;
        // 支給額の計算
        for($i = 1; $i <= self::MAX_SUM_HOURSLOT; $i++)
        {
            // 作業時間を DateInterval に変換
            $di = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[$i]);
            $unit_price = $this->rukuruUtilMoneyValue($this->SumWorkTypes[$i]['wt_pay']);
            $pay = $this->rukuruUtilDateIntervalToMoney($di, $unit_price);
            $this->SumWorkPays[$i] = $pay;
            $payAll += $pay;
        }
        // ？交通費？
        // 支給合計
        $this->SumWorkPayAll = $payAll;
 
        // 請求額の計算
        $billAll = 0;
        for($i = 1; $i <= self::MAX_SUM_HOURSLOT; $i++)
        {
            // 作業時間を DateInterval に変換
            $di = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[$i]);
            $unit_price = $this->rukuruUtilMoneyValue($this->SumWorkTypes[$i]['wt_bill']);
            $bill = $this->rukuruUtilDateIntervalToMoney($di, $unit_price);
            $this->SumWorkBills[$i] = $bill;
            $billAll += $bill;
        }
        // 請求合計
        $this->SumWorkBillAll = $billAll;
    }

    /**
     * データエリアをクリアする
     * @param $maxSlot 最大スロット数
     */
    protected function clearData($maxSlot)
    {
        parent::clearData($maxSlot);
    }

    /**
     * 勤怠読み込み
     */
    protected function fillTimekeepings()
    {
        $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $this->Client->cl_close_day);
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        // スロットのデータを読み出す
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            $targetDate = date('Y-m-d', $dt);
            $Slots = modelEmployeeWorks::where('employee_id', $this->employee_id)
                ->where('wrk_date', $targetDate)
                ->where('client_id', $this->client_id)
                ->orderBy('wrk_seq')
                ->get();

            foreach($Slots as $Slot)
            {
                $slotNo = $Slot->wrk_seq;
                // 有給レコードの場合
                if($Slot->leave)
                {
                    // 有給時間の正規化
                    $nLeave = strtotime($Slot->wrk_work_hours);
                    $sLeave = Date('H:i', $nLeave);
                    $this->TimekeepingDays[$dayIndex]['leave'] = $Slot->leave;
                    $this->TimekeepingDays[$dayIndex]['holiday_type'] = $Slot->holiday_type;
                    $this->TimekeepingDays[$dayIndex]['work_type'] = $Slot->work_type;
                    $this->TimekeepingDays[$dayIndex]['wrk_leave_hour' . $Slot->leave] = $sLeave;
                    $this->TimekeepingDays[$dayIndex]['notes'] = $Slot->notes;

                    $this->TimekeepingSlots[$dayIndex][$slotNo] = [
                        'wrk_seq' => $slotNo,
                        'wt_cd' => $Slot->wt_cd,
                        'wrk_log_start' => $Slot->wrk_log_start,
                        'wrk_log_end' => $Slot->wrk_log_end,
                        'wrk_work_start' => $Slot->wrk_work_start,
                        'wrk_work_end' => $Slot->wrk_work_end,
                        'wrk_work_hours' => null,
                        'class_bg_color' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'bg-gray-100' : '',
                        'readonly' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'readonly=\"readonly\"' : '',
                    ];
                    $this->calcSlot($dayIndex, $slotNo);
                    continue;
                }

                $slotHour = $this->calcSlotHour($slotNo, $Slot->holiday_type);
                if($slotNo == 1)
                {
                    $this->TimekeepingDays[$dayIndex]['leave'] = $Slot->leave;
                    $this->TimekeepingDays[$dayIndex]['holiday_type'] = $Slot->holiday_type;
                    $this->TimekeepingDays[$dayIndex]['work_type'] = $Slot->work_type;
                    $this->TimekeepingDays[$dayIndex]['notes'] = $Slot->notes;
                }
                $this->TimekeepingSlots[$dayIndex][$slotNo] = [
                    'wrk_seq' => $slotNo,
                    'wt_cd' => $Slot->wt_cd,
                    'wrk_log_start' => $Slot->wrk_log_start,
                    'wrk_log_end' => $Slot->wrk_log_end,
                    'wrk_work_start' => $Slot->wrk_work_start,
                    'wrk_work_end' => $Slot->wrk_work_end,
                    'class_bg_color' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'bg-gray-100' : '',
                    'readonly' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'readonly=\"readonly\"' : '',
                ];
                $this->TimekeepingSlots[$dayIndex][$slotHour]['wrk_work_hours'] = 
                    substr($Slot->wrk_work_hours, 0, 2) . ':' . substr($Slot->wrk_work_hours, 3, 2);
                $clientworktype = $this->PossibleWorkTypeFirst;
                // 勤務体系
                $work_type = $this->TimekeepingDays[$dayIndex]['work_type'];
                // 勤務の区切り時刻
                $worktype_time = $this->WorktypeRecords[$work_type]->worktype_time_end;
                $hhmmWorktypeTimeStart = date('H:i', strtotime($worktype_time));
                $this->calcSlot($dayIndex, $slotNo);
            }
            $dayIndex++;
        }
    }

    /**
     * mount function
     * */
    public function mount($workYear, $workMonth, $client_id, $clientplace_id, $employee_id)
    {
        parent::mount($workYear, $workMonth, $client_id, $clientplace_id, $employee_id);

        $this->clearData(self::MAX_TIMESLOT);         // 勤怠データ変数をクリアする
        $this->clearSummary();      // 集計をクリアする

        $this->PossibleWorkTypeFirst = modelClientWorktypes::possibleWorkTypeRecordFirst($this->client_id, $this->clientplace_id);
        
        // 単価
        $this->SumWorkTypes[1]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_std;    // 基本
        $this->SumWorkTypes[1]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_std;
        $this->SumWorkTypes[2]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;    // 残業
        $this->SumWorkTypes[2]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
        $this->SumWorkTypes[3]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;    // 深夜時間
        $this->SumWorkTypes[3]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
        $this->SumWorkTypes[4]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr_midnight;    // 深夜残業
        $this->SumWorkTypes[4]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr_midnight;
        $this->SumWorkTypes[5]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;    // 法定外休出
        $this->SumWorkTypes[5]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
        $this->SumWorkTypes[6]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr_midnight;    // 法定外深夜
        $this->SumWorkTypes[6]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr_midnight;
        $this->SumWorkTypes[7]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_holiday;    // 法定休出
        $this->SumWorkTypes[7]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_holiday;
        $this->SumWorkTypes[8]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_holiday_midnight;    //　法定深夜
        $this->SumWorkTypes[8]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_holiday_midnight;
        
        $this->fillTimekeepings();

        // スロットの背景色を設定する
        $this->SlotBGColors = [
            1 => 0,
            2 => 1,
            3 => 0,
            4 => 1,
        ];
    }

    public function render()
    {
        return view('livewire.employeeworksone');
    }

    /**
     * log start time change
     * @param $value 変更値
     * @param $day 日
     * @param $slotNo スロット番号
     * */
    public function logStartTimeChange($value, $day, $slotNo)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slotNo . '.wrk_log_start';

        // 勤務（平日、法定外休日、法定休日）
        $holiday_type = $this->TimekeepingDays[$day]['holiday_type'];        
        // 勤務体系
        $work_type = $this->TimekeepingDays[$day]['work_type'];
        // 勤務の区切り時刻
        $worktype_time = $this->WorktypeRecords[$work_type]->worktype_time_end;
        
        // 時間数表示インデクス
        $slotWorkHour = $this->calcSlotHour($slotNo, $holiday_type);

        // 勤務の区切り時刻
        $hhmmWorktypeTimeStart = date('H:i', strtotime($worktype_time));

        // 作業種別レコードを取得
        $wt_cd = $this->PossibleWorkTypeFirst->wt_cd;
        if($wt_cd == '')
        {   // 作業種別が未設定の場合は時間計算しない
            $this->addError($item, '作業種別');
            return;
        }

        // チェックを実行
        try {
            // エラーメッセージをリセット
            $this->resetErrorBag($item);
            $value = $this->rukuruUtilTimeNormalize($value);
        } catch (\Exception $e) {
            $this->addError($item, $e->getMessage());
            return;
        }

        $this->TimekeepingSlots[$day][$slotNo]['wrk_log_start'] = $value;
        $this->TimekeepingSlots[$day][$slotWorkHour]['wrk_work_hours'] = '';


        // 時間計算用のクラスインスタンスを作成
        $Slot = new TimeSlotOne(
            $this->TimekeepingDays[$day]['DateTime'],
            $hhmmWorktypeTimeStart, 
            intval($slotNo),
            $this->Client, 
            $this->PossibleWorkTypeFirst, 
            $this->TimekeepingSlots[$day][$slotNo]['wrk_log_start'],
            $this->TimekeepingSlots[$day][$slotNo]['wrk_log_end']
        );

        // 作業時間を計算
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_start'] = $Slot->getWorkStart();
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_end'] = $Slot->getWorkEnd();
        $this->TimekeepingSlots[$day][$slotWorkHour]['wrk_work_hours'] = $Slot->getWorkHours();

        // 集計作業
        $this->calcSlot($day, $slotNo);
    }

    /**
     * log end time change
     * @param $value 変更値
     * @param $day 日
     * @param $slotNo スロット番号
     * */
    public function logEndTimeChange($value, $day, $slotNo)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slotNo . '.wrk_log_end';

        // 勤務（平日、法定外休日、法定休日）
        $holiday_type = $this->TimekeepingDays[$day]['holiday_type'];        
        // 勤務体系
        $work_type = $this->TimekeepingDays[$day]['work_type'];
        // 勤務の区切り時刻
        $worktype_time = $this->WorktypeRecords[$work_type]->worktype_time_end;

        // 時間数表示インデクス
        $slotWorkHour = $this->calcSlotHour($slotNo, $holiday_type);

        // 勤務の区切り時刻
        $hhmmWorktypeTimeStart = date('H:i', strtotime($worktype_time));

        // 作業種別レコードを取得
        $wt_cd = $this->PossibleWorkTypeFirst->wt_cd;
        if($wt_cd == '')
        {   // 作業種別が未設定の場合は時間計算しない
            $this->addError($item, '作業種別');
            return;
        }

        // チェックを実行
        try {
            // エラーメッセージをリセット
            $this->resetErrorBag($item);
            $value = $this->rukuruUtilTimeNormalize($value);
        } catch (\Exception $e) {
            $this->addError($item, $e->getMessage());
            return;
        }

        $this->TimekeepingSlots[$day][$slotNo]['wrk_log_end'] = $value;
        $this->TimekeepingSlots[$day][$slotWorkHour]['wrk_work_hours'] = '';
        
        // 時間計算用のクラスインスタンスを作成
        $Slot = new TimeSlotOne(
            $this->TimekeepingDays[$day]['DateTime'],
            $hhmmWorktypeTimeStart, 
            intval($slotNo),
            $this->Client, 
            $this->PossibleWorkTypeFirst, 
            $this->TimekeepingSlots[$day][$slotNo]['wrk_log_start'],
            $this->TimekeepingSlots[$day][$slotNo]['wrk_log_end']
        );

        // 作業時間を計算
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_start'] = $Slot->getWorkStart();
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_end'] = $Slot->getWorkEnd();
        $this->TimekeepingSlots[$day][$slotWorkHour]['wrk_work_hours'] = $Slot->getWorkHours();

        // 集計作業
        $this->calcSlot($day, $slotNo);
    }

    /**
     * 有給種別が変更された
     * @param $value 変更値
     * @param $day 日
     */
    public function yukyuTypeChange($value, $day)
    {
        // 項目名
        $item = 'TimekeepingDays.' . $day . '.leave';

        // 集計作業
        $this->calcSlot($day, 1);
    }

    /**
     * 有給時間が変更された
     * @param $value 変更値
     * @param $day 日
     * @param $slotNo 時間番号 [1, 2]
     * */
    public function yukyuTimeChange($value, $day, $slotNo)
    {
        // 項目名
        $item = 'TimekeepingDays.' . $day . '.wrk_leave_hour' . $slotNo;

        // チェックを実行
        try {
            // エラーメッセージをリセット
            $this->resetErrorBag($item);
            $value = $this->rukuruUtilTimeNormalize($value);
        } catch (\Exception $e) {
            $this->addError($item, $e->getMessage());
            return;
        }

        $this->TimekeepingDays[$day]['wrk_leave_hour' . $slotNo] = $value;

        // 集計作業
        $this->calcSlot($day, $slotNo);
    }

    /**
     * slotNo から休憩時間を返す
     */
    protected function getBreakTime($slotNo)
    {
        switch($slotNo)
        {
            case 1: // 就業
                $breakTime = $this->PossibleWorkTypeFirst->wt_lunch_break;
                break;
            case 2: // 普通残業
                $breakTime = $this->PossibleWorkTypeFirst->wt_evening_break;
                break;
            case 3: // 深夜時間
                $breakTime = $this->PossibleWorkTypeFirst->wt_night_break;
                break;
            case 4: // 深夜残業
                $breakTime = $this->PossibleWorkTypeFirst->wt_midnight_break;
                break;
            default:
                $breakTime = '';
                break;
        }
        return $breakTime;
    }

    /**
     * save work time
     */
    protected function insertEmployeeWork()
    {
        foreach($this->TimekeepingDays as $dayIndex => $Day)
        {
            for($slotNo = 1; $slotNo < self::MAX_TIMESLOT; $slotNo++)
            {
                if(! $this->mustWriteSlot($dayIndex, $slotNo))
                {
                    continue;
                }

                // 有給の場合
                if(!empty($this->TimekeepingDays[$dayIndex]['leave']) && $slotNo == 1)
                {
                    $leave = $this->TimekeepingDays[$dayIndex]['leave'] ;    // 有給の種類
                    $hhmmLeave = $this->TimekeepingDays[$dayIndex]['wrk_leave_hour' . $leave]; // 有給時間
                    $hourlyPay = $this->rukuruUtilMoneyValue($this->Employee->empl_paid_leave_pay, 0);
                    $diLeave = $this->rukuruUtilTimeToDateInterval($hhmmLeave);
                    $leavePay =$this->rukuruUtilDateIntervalToMoney($diLeave, $hourlyPay);

                    $Work = new modelEmployeeWorks();
                    $Work->employee_id = $this->employee_id;
                    $Work->wrk_date = $this->TimekeepingDays[$dayIndex]['date'];
                    $Work->wrk_seq = $slotNo;
                    $Work->leave = $this->TimekeepingDays[$dayIndex]['leave'];
                    $Work->client_id = $this->client_id;
                    $Work->clientplace_id = $this->clientplace_id;
                    $Work->holiday_type = 0;
                    $Work->work_type = 1;
                    $Work->wt_cd = '';
                    $Work->wt_name = ($leave == 1) ? '有休' : '特休';
    
                    $Work->wrk_log_start = null;
                    $Work->wrk_log_end = null;
                    $Work->wrk_work_start = null;
                    $Work->wrk_work_end = null;
                    $Work->wrk_break = null;
                    $Work->wrk_work_hours = $this->TimekeepingDays[$dayIndex]['wrk_leave_hour' . $leave];
    
                    $Work->summary_index = 1;       // 1 で良いか？
                    $Work->summary_name = $Work->wt_name;
                    
                    $Work->payhour = 0;
                    $Work->wrk_pay = $leavePay;
                    $Work->billhour = 0;
                    $Work->wrk_bill = 0;
    
                    $Work->notes = $this->TimekeepingDays[$dayIndex]['notes'];    
                    $Work->save();
                    continue;
                }

                $slotHour = $this->calcSlotHour($slotNo, $Day['holiday_type']);

                $Slot = $this->TimekeepingSlots[$dayIndex][$slotNo];
                $SlotWorkHour = $this->TimekeepingSlots[$dayIndex][$slotHour];

                $Work = new modelEmployeeWorks();
                $Work->employee_id = $this->employee_id;
                $Work->wrk_date = $this->TimekeepingDays[$dayIndex]['date'];
                $Work->wrk_seq = $slotNo;
                $Work->leave = ($slotNo == 1) ? $this->TimekeepingDays[$dayIndex]['leave'] : null;
                $Work->client_id = $this->client_id;
                $Work->clientplace_id = $this->clientplace_id;
                $Work->holiday_type = $this->TimekeepingDays[$dayIndex]['holiday_type'];
                $Work->work_type = $this->TimekeepingDays[$dayIndex]['work_type'];
                $Work->wt_cd = $this->PossibleWorkTypeFirst->wt_cd;
                $Work->wt_name = $this->PossibleWorkTypeFirst->wt_name;

                $Work->wrk_log_start = empty($Slot['wrk_log_start']) ? null : $Slot['wrk_log_start'];
                $Work->wrk_log_end = empty($Slot['wrk_log_end']) ? null : $Slot['wrk_log_end'];
                $Work->wrk_work_start = empty($Slot['wrk_work_start']) ? null : $Slot['wrk_work_start'];
                $Work->wrk_work_end = empty($Slot['wrk_work_end']) ? null : $Slot['wrk_work_end'];
                $Work->wrk_break = $this->getBreakTime($slotNo);
                $Work->wrk_work_hours = empty($SlotWorkHour['wrk_work_hours']) ? null : $SlotWorkHour['wrk_work_hours'];

                $Work->summary_index = $slotNo;
                $slotSumNo = $this->calcSlotSum($slotNo, $Work->holiday_type);
                $Work->summary_name = $this->SumWorkTypes[$slotSumNo]['wt_name'];

                $diSlotWorkHours = $this->rukuruUtilTimeToDateInterval(empty($SlotWorkHour['wrk_work_hours']) ? '00:00' : $SlotWorkHour['wrk_work_hours']);
                $Work->payhour = $this->rukuruUtilMoneyValue($this->SumWorkTypes[$slotNo]['wt_pay']);
                $Work->wrk_pay = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->payhour);
                $Work->payhour = $this->rukuruUtilMoneyValue($this->SumWorkTypes[$slotNo]['wt_bill']);
                $Work->wrk_bill = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->billhour);

                $Work->notes = $this->TimekeepingDays[$dayIndex]['notes'];
                $Work->save();
            }
        }
    }
}
