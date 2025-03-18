<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;
use App\Services\TimeSlotOne;

use App\Models\applogs;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;
use App\Models\worktype as modelWorktypes;
use App\Models\employees as modelEmployees;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeepays as modelEmployeePays;
use App\Models\salary as modelSalary;

/**
 * 勤怠入力画面標準
 */
class Employeeworksone extends EmployeeworksBase
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants
    private const MAX_TIMESLOT = 4;
    private const MAX_SUM_HOURSLOT = 8; // 時間集計用スロット数

    /**
     * possible work types
     */
    public $PossibleWorkTypeFirst;

    /**
     * 勤務体系
     */
    public $WorktypeRecords;

    // 計算用
    public $hourlyWage = [];        // 時給 [スロット番号][支給請求の別]

    public $DayHasWorkShukkin = [];        // 出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkKyujitsu = [];        // 法定外休日出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkHoutei = [];        // 法定休日出勤日数 [$dayIndex] ごとの作業の有無
    public $DayHasWorkYukyu = [];        // 有給休暇日数 [$dayIndex] ごとの作業の有無

    /**
     * 集計表示用
     */
    public $SumDaysShukkin;            // 日数 出勤
    public $SumDaysKyujitsu;           // 日数 法定外休日
    public $SumDaysHoutei;             // 日数 法定休日
    public $SumDaysYukyu;              // 日数 有給

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
        // 出勤日数
        $this->SumDaysShukkin = 0;            // 平日出勤日数
        $this->SumDaysKyujitsu = 0;            // 休日出勤日数
        $this->SumDaysHoutei = 0;       // 法定休日出勤日数
        $this->SumDaysYukyu = 0;            // 有給休暇日数
  
        // 1ヶ月の作業時間合計
        $this->SumWorkHours = array_fill(1, self::MAX_SUM_HOURSLOT, '0:00');  // 作業時間合計
        $this->SumWorkHoursAll = '0:00';  // 1ヶ月の作業時間合計
 
        $this->SumWorkPays = array_fill(1, self::MAX_SUM_HOURSLOT, 0);  // 支給額合計
        $this->SumWorkPayAll = 0;   // 1ヶ月の支給額合計

        $this->SumWorkBills = array_fill(1, self::MAX_SUM_HOURSLOT, 0);  // 請求額合計
        $this->SumWorkBillAll = 0;   // 1ヶ月の請求額合計

        // 作業種別名、基本時給、請求額をクリア
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
        // 計算時給
        for($slotNo=1; $slotNo<=8; $slotNo++)
        {
            $this->hourlyWage[$slotNo]['wt_pay'] = 0;
            $this->hourlyWage[$slotNo]['wt_bill'] = 0;
        }
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
            case 2: // 普通残業
                switch($holiday_type)
                {
                    case 1: // 法定外休日
                        $slotSum = 5;
                        break;
                    case 2: // 法定休日
                        $slotSum = 7;
                        break;
                    default:
                        $slotSum = $slotNo;
                        break;
                }
                break;
            case 3: // 深夜時間
            case 4: // 深夜残業
                switch($holiday_type)  // 勤務
                {
                    case 1: // 法定外休日
                        $slotSum = 6;
                        break;
                    case 2: // 法定休日
                        $slotSum = 8;
                        break;
                    default:
                        $slotSum = $slotNo;
                        break;
                }
                break;
            default:
                $slotSum = $slotNo;
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
        for($slotSum = 1; $slotSum <= self::MAX_SUM_HOURSLOT; $slotSum++)
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
                // 集計スロット番号を計算する
                $slotSum = $this->calcSlotSum($slotNo, $holiday_type);

                $hhmmHour = empty($this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours']) ? 
                    '' : $this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours']; // 時間 hh:mm
                if(!empty($hhmmHour))
                {
                    $diHour = $this->rukuruUtilTimeToDateInterval($hhmmHour);
                    $diSumWorkHours[$slotSum] = $this->rukuruUtilDateIntervalAdd($diSumWorkHours[$slotSum], $diHour);
                }
            }
            $i++;
        }

        $diHourAll = new DateInterval('P0D');  // DateInterval 作業時間合計
        for($i = 1; $i <= self::MAX_SUM_HOURSLOT; $i++)
        {
            $diHourAll = $this->rukuruUtilDateIntervalAdd($diHourAll, $diSumWorkHours[$i]);
            $this->SumWorkHours[$i] = $this->rukuruUtilDateIntervalFormat($diSumWorkHours[$i]);
        }
        $this->SumWorkHoursAll = $this->rukuruUtilDateIntervalFormat($diHourAll);
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
        // 日数合計を求める
        // 有給
        if($this->TimekeepingDays[$day]['leave'])
        {
            $this->DayHasWorkYukyu[$day] = 1;
            $this->SumDaysYukyu = array_sum($this->DayHasWorkYukyu);
            return;
        }

        // 1 日の作業時間合計を計算
        $dayWorkHours = $this->sumDayWorkHours($day, self::MAX_TIMESLOT);  // DateInterval object
        $hhmmWorkHours = $this->rukuruUtilDateIntervalFormat($dayWorkHours); // 時間 hh:mm
        $presence = ($hhmmWorkHours == '0:00') ? 0 : 1;

        // 勤務区分 0: 平日, 1: 法定外休日, 2: 法定休日）
        $holiday_type = $this->TimekeepingDays[$day]['holiday_type'];

        // 作業日数合計
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

        // 時間スロットの1ヶ月の作業時間合計
        $diWorkHours = $this->calcMonthWorkHours();

        // 支給合計
        $payAll = 0;
        // 支給額の計算
        for($i = 1; $i <= self::MAX_SUM_HOURSLOT; $i++)
        {
            // 作業時間を DateInterval に変換
            $di = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[$i]);
            $unit_price = $this->rukuruUtilMoneyValue($this->hourlyWage[$i]['wt_pay']);
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
            $unit_price = $this->rukuruUtilMoneyValue($this->hourlyWage[$i]['wt_bill']);
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
     * 従業員給与を更新する
     */
    protected function updateSalary()
    {
        $Salary = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        if(empty($Salary))
        {
            $Salary = new modelSalary();
            $Salary->employee_id = $this->employee_id;
            $Salary->work_year = $this->workYear;
            $Salary->work_month = $this->workMonth;
            $Salary->paid_leave_pay = 0;    // 有給日当
            $Salary->non_statutory_days = 0;    // 法定外休日
            $Salary->statutory_days = 0;    // 法定休日
            $Salary->work_amount = 0;    // 作業金額
            $Salary->allow_amount = 0;    // 手当金額
            $Salary->deduct_amount = 0;    // 控除金額
            $Salary->transport = 0;    // 交通費
            $Salary->pay_amount = 0;    // 支給金額
        }

        $Salary->working_regular_days = $this->SumDaysShukkin;
        $Salary->non_statutory_days = $this->SumDaysKyujitsu;
        $Salary->statutory_days = $this->SumDaysHoutei;
        $Salary->paid_leave_days = $this->SumDaysYukyu;
        $Salary->working_days = $this->SumDaysShukkin + $this->SumDaysKyujitsu + $this->SumDaysHoutei + $this->SumDaysYukyu;
        $Salary->save();
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
                    $this->TimekeepingDays[$dayIndex]['leave'] = $Slot->leave ? true : false;
                    $this->TimekeepingDays[$dayIndex]['holiday_type'] = $Slot->holiday_type;
                    $this->TimekeepingDays[$dayIndex]['work_type'] = $Slot->work_type;
                    $this->TimekeepingDays[$dayIndex]['notes'] = $Slot->notes;
                    $this->TimekeepingDays[$dayIndex]['rowColor'] = $this->holidayTypeColor($Slot->holiday_type);

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
                $this->TimekeepingSlots[$dayIndex][$slotNo]['wrk_work_hours'] = $Slot->wrk_work_hours;
                $clientworktype = $this->PossibleWorkTypeFirst;
                // 勤務体系
                $work_type = $this->TimekeepingDays[$dayIndex]['work_type'];
                // 勤務の区切り時刻
                $worktype_time = $this->WorktypeRecords[$work_type]->worktype_time_end;
                $hhmmWorktypeTimeStart = Date('G:i', strtotime($worktype_time));
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
        if(!$this->PossibleWorkTypeFirst)
        {
            session()->flash('error', __('Work Type') . ' ' . __('Not Found'));
            return redirect()->route('workemployee');
        }

        // 従業員の時給特例を取得
        $EmployeePay = modelEmployeePays::where('employee_id', $this->employee_id)
            ->where('clientworktype_id', $this->PossibleWorkTypeFirst->id)
            ->first();
        if($EmployeePay)
        {
            $this->SumWorkTypes[1]['wt_pay'] = $EmployeePay->wt_pay_std;
            $this->SumWorkTypes[1]['wt_bill'] = $EmployeePay->wt_bill_std;
            $this->SumWorkTypes[2]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->SumWorkTypes[2]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->SumWorkTypes[3]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->SumWorkTypes[3]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->SumWorkTypes[4]['wt_pay'] = $EmployeePay->wt_pay_ovr_midnight;
            $this->SumWorkTypes[4]['wt_bill'] = $EmployeePay->wt_bill_ovr_midnight;
            $this->SumWorkTypes[5]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->SumWorkTypes[5]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->SumWorkTypes[6]['wt_pay'] = $EmployeePay->wt_pay_ovr_midnight;
            $this->SumWorkTypes[6]['wt_bill'] = $EmployeePay->wt_bill_ovr_midnight;
            $this->SumWorkTypes[7]['wt_pay'] = $EmployeePay->wt_pay_holiday;
            $this->SumWorkTypes[7]['wt_bill'] = $EmployeePay->wt_bill_holiday;
            $this->SumWorkTypes[8]['wt_pay'] = $EmployeePay->wt_pay_holiday_midnight;
            $this->SumWorkTypes[8]['wt_bill'] = $EmployeePay->wt_bill_holiday_midnight;
            // 計算用時給
            $this->hourlyWage[1]['wt_pay'] = $EmployeePay->wt_pay_std;
            $this->hourlyWage[1]['wt_bill'] = $EmployeePay->wt_bill_std;
            $this->hourlyWage[2]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->hourlyWage[2]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->hourlyWage[3]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->hourlyWage[3]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->hourlyWage[4]['wt_pay'] = $EmployeePay->wt_pay_ovr_midnight;
            $this->hourlyWage[4]['wt_bill'] = $EmployeePay->wt_bill_ovr_midnight;
            $this->hourlyWage[5]['wt_pay'] = $EmployeePay->wt_pay_ovr;
            $this->hourlyWage[5]['wt_bill'] = $EmployeePay->wt_bill_ovr;
            $this->hourlyWage[6]['wt_pay'] = $EmployeePay->wt_pay_ovr_midnight;
            $this->hourlyWage[6]['wt_bill'] = $EmployeePay->wt_bill_ovr_midnight;
            $this->hourlyWage[7]['wt_pay'] = $EmployeePay->wt_pay_holiday;
            $this->hourlyWage[7]['wt_bill'] = $EmployeePay->wt_bill_holiday;
            $this->hourlyWage[8]['wt_pay'] = $EmployeePay->wt_pay_holiday_midnight;
            $this->hourlyWage[8]['wt_bill'] = $EmployeePay->wt_bill_holiday_midnight;
        }
        else{
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
            // 計算用時給
            $this->hourlyWage[1]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_std;
            $this->hourlyWage[1]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_std;
            $this->hourlyWage[2]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;
            $this->hourlyWage[2]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
            $this->hourlyWage[3]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;
            $this->hourlyWage[3]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
            $this->hourlyWage[4]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr_midnight;
            $this->hourlyWage[4]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr_midnight;
            $this->hourlyWage[5]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr;
            $this->hourlyWage[5]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr;
            $this->hourlyWage[6]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_ovr_midnight;
            $this->hourlyWage[6]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_ovr_midnight;
            $this->hourlyWage[7]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_holiday;
            $this->hourlyWage[7]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_holiday;
            $this->hourlyWage[8]['wt_pay'] = $this->PossibleWorkTypeFirst->wt_pay_holiday_midnight;
            $this->hourlyWage[8]['wt_bill'] = $this->PossibleWorkTypeFirst->wt_bill_holiday_midnight;
        }
        
        $this->fillTimekeepings();
    }

    public function render()
    {
        return view('livewire.employeeworksone');
    }

    /**
     * 有給フラグの変更
     * param bool $value 有給フラグ, int $day 日
     * return void
     */
    public function leaveChange($value, $day)
    {
        $this->TimekeepingDays[$day]['leave'] = $value;
        for($slotNo=1; $slotNo<=self::MAX_TIMESLOT; $slotNo++)
        {
            if($value)
            {
                // 時間出勤、退勤時刻をクリア
                $this->TimekeepingSlots[$day][$slotNo]['wrk_log_start'] = null;
                $this->TimekeepingSlots[$day][$slotNo]['wrk_log_end'] = null;
            }
            $this->TimekeepingSlots[$day][$slotNo]['class_bg_color'] = $value ? 'bg-gray-100' : '';
            $this->TimekeepingSlots[$day][$slotNo]['readonly'] = $value ? 'readonly=\"readonly\"' : '';

            $this->calcSlot($day, $slotNo);
        }
    }

    /**
     * 休日区分による文字色
     */
    public function holidayTypeColor($holiday_type)
    {
        switch($holiday_type)
        {
            case 1: // 法定外休日
                return 'color: blue;';
            case 2: // 法定休日
                return 'color: red;';
            default:
                return '';
        }
    }
    /**
     * 休日区分（勤務）が変わった
     */
    public function holidayTypeChange($value, $day)
    {
        // 項目名
        $item = 'TimekeepingDays.' . $day . '.holiday_type';
    }

    /**
     * 勤務体系が変わった
     */
    public function workTypeChange($value, $day)
    {
        // 項目名
        $item = 'TimekeepingDays.' . $day . '.work_type';
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

        // 勤務の区切り時刻
        $hhmmWorktypeTimeStart = Date('G:i', strtotime($worktype_time));

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
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours'] = '';


        // 時間計算用のクラスインスタンスを作成
        try {
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
            $this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours'] = $Slot->getWorkHours();
    
            // 集計作業
            $this->calcSlot($day, $slotNo);
        } catch (\Exception $e) {
            $this->addError($item, '計算');
        }
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

        // 勤務の区切り時刻
        $hhmmWorktypeTimeStart = Date('G:i', strtotime($worktype_time));

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
        $this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours'] = '';
        
        // 時間計算用のクラスインスタンスを作成
        try {
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
            $this->TimekeepingSlots[$day][$slotNo]['wrk_work_hours'] = $Slot->getWorkHours();
    
            // 集計作業
            $this->calcSlot($day, $slotNo);
        } catch (\Exception $e) {
            $this->addError($item, '計算');
        }
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
     * 1日の勤怠をクリアする
     * @param int $day インデクス
     */
    public function deleteTimekeepingDay($dayIndex)
    {
        for($slotNo=1; $slotNo<=self::MAX_TIMESLOT; $slotNo++)
        {
            $this->TimekeepingSlots[$dayIndex][$slotNo] = [
                'wrk_seq' => $slotNo,
                'wt_cd' => '',
                'wrk_log_start' => null,
                'wrk_log_end' => null,
                'wrk_work_start' => null,
                'wrk_work_end' => null,
                'wrk_work_hours' => null,
                'class_bg_color' => '',
                'readonly' => '',
            ];
            $this->calcSlot($dayIndex, $slotNo);
        }
    }

    /**
     * save work time
     */
    protected function insertEmployeeWork()
    {
        foreach($this->TimekeepingDays as $dayIndex => $Day)
        {
            for($slotNo = 1; $slotNo <= self::MAX_TIMESLOT; $slotNo++)
            {
                if(! $this->mustWriteSlot($dayIndex, $slotNo))
                {
                    continue;
                }

                // 有給の場合
                if(!empty($this->TimekeepingDays[$dayIndex]['leave']) && $slotNo == 1)
                {
                    $Work = new modelEmployeeWorks();
                    $Work->employee_id = $this->employee_id;
                    $Work->wrk_date = $this->TimekeepingDays[$dayIndex]['date'];
                    $Work->wrk_seq = $slotNo;
                    $Work->leave = $this->TimekeepingDays[$dayIndex]['leave'] ? 1 : null;
                    $Work->client_id = $this->client_id;
                    $Work->clientplace_id = $this->clientplace_id;
                    $Work->holiday_type = 0;
                    $Work->work_type = 1;
                    $Work->wt_cd = '';
                    $Work->wt_name = '有休';
    
                    $Work->wrk_log_start = null;
                    $Work->wrk_log_end = null;
                    $Work->wrk_work_start = null;
                    $Work->wrk_work_end = null;
                    $Work->wrk_break = null;
                    $Work->wrk_work_hours = null;
    
                    $Work->summary_index = 1;       // 1 で良いか？
                    $Work->summary_name = '有休';
                    
                    $Work->payhour = 0;
                    $Work->wrk_pay = $this->rukuruUtilMoneyValue($this->getEmployeeLeaveDays($this->employee_id), 0);
                    $Work->billhour = 0;
                    $Work->wrk_bill = 0;
    
                    $Work->notes = $this->TimekeepingDays[$dayIndex]['notes'];    
                    $Work->save();
                    continue;
                }

                $Slot = $this->TimekeepingSlots[$dayIndex][$slotNo];

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
                $Work->wrk_work_hours = empty($Slot['wrk_work_hours']) ? null : $Slot['wrk_work_hours'];

                $Work->summary_index = $slotNo;
                $slotSumNo = $this->calcSlotSum($slotNo, $Work->holiday_type);
                $Work->summary_name = $this->SumWorkTypes[$slotSumNo]['wt_name'];

                $hhmmWorkHours = empty($this->SumWorkHours[$slotSumNo]) ? '0:00' : $this->SumWorkHours[$slotSumNo];
                $diSlotWorkHours = $this->rukuruUtilTimeToDateInterval($hhmmWorkHours);
                $Work->payhour = $this->rukuruUtilMoneyValue($this->hourlyWage[$slotSumNo]['wt_pay']);
                $Work->wrk_pay = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->payhour);
                $Work->billhour = $this->rukuruUtilMoneyValue($this->hourlyWage[$slotSumNo]['wt_bill']);
                $Work->wrk_bill = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->billhour);

                $Work->notes = $this->TimekeepingDays[$dayIndex]['notes'];
                $Work->save();
            }
        }
        $this->updateSalary();
    }
}
