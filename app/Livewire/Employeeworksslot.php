<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;
use App\Services\TimeSlotSlot;

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
 * スロットごと勤怠入力画面
 */
class Employeeworksslot extends EmployeeworksBase
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants
    private const MAX_SLOTS = 7;

    /**
     * 作業種別
     */
    public $PossibleWorkTypeRecords;
    public $WorkTypes = [];

    /**
     * スロットの背景色
     */
    public $SlotBGColors = [];

    /**
     * timekeeping array
     */
    public $TimekeepingTypes = [];  // スロットごとの情報
                                    // ['wt_cd' => 作業種別]

    // 計算用
    public $DayHasWork = [];        // 日にち [$dayIndex] ごとの作業の有無
                                    // 0 作業なし, 1 作業あり

    /**
     * 集計表示用
     */
    public $SumDays;            // 日数
    public $SumDaysYukyu;       // 有給日数
    public $SumWorkPayYukyu;        // 有給支給額

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
        return 1;
    }

    /**
     * 作業種別クリア
     * @param $slot スロット番号
     * @return void
     */
    protected function clearWorkType($slot)
    {
        // 作業種別名、時給、請求額をクリア
        $this->SumWorkTypes[$slot]['wt_cd'] = '';
        $this->SumWorkTypes[$slot]['wt_name'] = '';
        $this->SumWorkTypes[$slot]['wt_pay_std'] = '';
        $this->SumWorkTypes[$slot]['wt_bill_std'] = '';
    }

    /**
     * 作業種別設定
     * @param $slot スロット番号
     * @param $wt_cd 作業種別
     * @return void
     */
    protected function setWorkType($slot, $wt_cd)
    {
        // 作業種別名、時給、請求額を設定
        $this->SumWorkTypes[$slot]['wt_cd'] = $wt_cd;
        $this->SumWorkTypes[$slot]['wt_name'] = $wt_cd . ' ' . $this->PossibleWorkTypeRecords[$wt_cd]->wt_name;
        $this->SumWorkTypes[$slot]['wt_pay_std'] = $this->PossibleWorkTypeRecords[$wt_cd]->wt_pay_std;
        $this->SumWorkTypes[$slot]['wt_bill_std'] = $this->PossibleWorkTypeRecords[$wt_cd]->wt_bill_std;
    }

    /**
     * 集計エリアをクリアする
     */
    protected function clearSummary()
    {
        $this->SumDays = 0;            // 日数
        $this->SumDaysYukyu = 0;       // 有給日数
        $this->SumWorkPayYukyu = 0;    // 有給支給額

        $this->SumWorkHours = array_fill(1, self::MAX_SLOTS, null);  // 作業時間合計
        $this->SumWorkHoursAll = '0:00';  // 1ヶ月の作業時間合計
        $this->SumWorkPays = array_fill(1, self::MAX_SLOTS, 0);  // 支給額合計
        $this->SumWorkPayAll = 0;   // 1ヶ月の支給額合計
        $this->SumWorkBills = array_fill(1, self::MAX_SLOTS, 0);  // 請求額合計
        $this->SumWorkBillAll = 0;   // 1ヶ月の請求額合計

        // 作業種別名、時給、請求額をクリア
        $this->SumWorkTypes=[];
        for($i = 1; $i <= self::MAX_SLOTS; $i++)
        {
            $this->clearWorkType($i);
        }
    }

    /**
     * 作業種別が変更された場合の再設定、再計算処理
     * @param $slot スロット番号
     * @return void
     */
    protected function calcWorkType($slot)
    {
        // 作業種別レコードを取得
        $wt_cd = $this->TimekeepingTypes[$slot]['wt_cd'];
        // 作業種別名、時給、請求額を表示
    }

    /**
     * 1ヶ月の作業時間合計を計算
     */
    protected function sumAllWorkHours()
    {
        $allHours = new DateInterval('P0D');  // DateInterval object
        for($i = 1; $i <= self::MAX_SLOTS; $i++)
        {
            if($this->SumWorkHours[$i] != '')
            {
                $workHours = $this->rukuruUtilTimeToDateInterval($this->SumWorkHours[$i]);
                $allHours = $this->rukuruUtilDateIntervalAdd($allHours, $workHours);
            }
        }
        return $allHours;
    }

    /**
     * 1スロットの1ヶ月作業時間合計を計算
     * @param $slot スロット
     * @return dateInterval 作業時間合計
     */
    protected function sumSlotWorkHours($slot)
    {
        $slotWorkHours = new DateInterval('P0D');  // DateInterval object
        $i = 1;
        foreach($this->TimekeepingDays as $day => $Day)
        {
            if(!empty($this->HoursSlotDay[$slot][$i]))
            {
                $hhmmHours = $this->HoursSlotDay[$slot][$i];
                $workHours =  $this->rukuruUtilTimeToDateInterval($hhmmHours);
                $slotWorkHours = $this->rukuruUtilDateIntervalAdd($slotWorkHours, $workHours);
            }
            $i++;
        }
        return $slotWorkHours;
    }

    /**
     * 1つのスロットの時間が変更されたら以下を再計算する
     * 1. 作業日数合計
     * 2. スロットの作業時間合計
     * 3. スロットの支給額合計
     * 4. スロットの請求額合計
     * @param $day 日
     * @param $slot スロット
     * @param $payPerHour 時給
     * @param $billPerHour 請求単価
     */
    protected function calcSlot($day, $slot, $payPerHour, $billPerHour)
    {
        // 1 日の作業時間合計を計算
        $dayWorkHours = $this->sumDayWorkHours($day, self::MAX_SLOTS);  // DateInterval object
        $hhmmDayWorkHours = $this->rukuruUtilDateIntervalFormat($dayWorkHours);

        // 作業日数合計
        $this->DayHasWork[$day] = ($hhmmDayWorkHours == '00:00') ? 0 : 1;   // 作業がある日は1、ない日は0
        $this->SumDays = array_sum($this->DayHasWork);

        // 有給日数と支給額を再計算
        if($this->TimekeepingDays[$day]['leave'])
        {
            $this->SumDaysYukyu++;
            $this->SumWorkPayYukyu += $this->rukuruUtilMoneyValue($this->Employee->empl_paid_leave_pay, 0);
        }

        // スロットの作業時間合計
        $hhmmDaySlotWorkHours = $this->TimekeepingSlots[$day][$slot]['wrk_work_hours']; // スロットの作業時間 hh:mm
        $hhmmDaySlotWorkHours = ($hhmmDaySlotWorkHours == '') ? '00:00' : $hhmmDaySlotWorkHours;
        $this->HoursSlotDay[$slot][$day] = $hhmmDaySlotWorkHours;

        // スロットの1ヶ月の作業時間合計
        $diWorkHours = $this->sumSlotWorkHours($slot);
        $this->SumWorkHours[$slot] = $this->rukuruUtilDateIntervalFormat($diWorkHours);
        // 1ヶ月の作業時間合計を計算
        $allHours = $this->sumAllWorkHours();  // DateInterval object
        $this->SumWorkHoursAll = $this->rukuruUtilDateIntervalFormat($allHours);

        // スロットの支給・請求額
        $payPerHour = $this->rukuruUtilMoneyValue($payPerHour, 0);
        $billPerHour = $this->rukuruUtilMoneyValue($billPerHour, 0);

        $diSlotWorkHours = $this->rukuruUtilTimeToDateInterval($hhmmDaySlotWorkHours);
        $this->PaySlotDay[$slot][$day] = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $payPerHour);
        $this->BillSlotDay[$slot][$day] = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $billPerHour);

        // 1ヶ月の支給・請求額合計
        $this->SumWorkPays[$slot] = array_sum($this->PaySlotDay[$slot]);
        $this->SumWorkBills[$slot] = array_sum($this->BillSlotDay[$slot]);

        // 1ヶ月全スロットの支給額合計
        $this->SumWorkPayAll = array_sum($this->SumWorkPays) + $this->SumWorkPayYukyu;
        // 1ヶ月全スロットの請求額合計
        $this->SumWorkBillAll = array_sum($this->SumWorkBills);
    }

    /**
     * データエリアをクリアする
     * @param $maxSlot 最大スロット数
     */
    protected function clearData($maxSlot)
    {
        parent::clearData($maxSlot);

        $this->TimekeepingTypes = [];   // スロットごとの情報

        // スロットごとの情報をクリアする
        for($slotNo=1; $slotNo<=$maxSlot; $slotNo++)
        {
            $this->TimekeepingTypes[$slotNo] = '';
        }
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
                    $this->TimekeepingDays[$dayIndex]['leave'] = true;
                    $this->TimekeepingDays[$dayIndex]['holiday_type'] = $Slot->holiday_type;
                    $this->TimekeepingDays[$dayIndex]['work_type'] = $Slot->work_type;
                    $this->TimekeepingDays[$dayIndex]['notes'] = $Slot->notes;

                    $this->TimekeepingTypes[$slotNo] = '';

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
                    $this->calcSlot($dayIndex, $slotNo, 0, 0);
                    continue;
                }

                if(empty($this->SumWorkTypes[$slotNo]['wt_cd']))
                {
                    $this->setWorkType($slotNo, $Slot->wt_cd);
                }
                if($slotNo == 1)
                {
                    $this->TimekeepingDays[$dayIndex]['leave'] = $Slot->leave;
                    $this->TimekeepingDays[$dayIndex]['holiday_type'] = $Slot->holiday_type;
                    $this->TimekeepingDays[$dayIndex]['work_type'] = $Slot->work_type;
                    $this->TimekeepingDays[$dayIndex]['notes'] = $Slot->notes;
                }
                $this->TimekeepingTypes[$slotNo] = $Slot->wt_cd;
                $this->TimekeepingSlots[$dayIndex][$slotNo] = [
                    'wrk_seq' => $slotNo,
                    'wt_cd' => $Slot->wt_cd,
                    'wrk_log_start' => $Slot->wrk_log_start,
                    'wrk_log_end' => $Slot->wrk_log_end,
                    'wrk_work_start' => $Slot->wrk_work_start,
                    'wrk_work_end' => $Slot->wrk_work_end,
                    'wrk_work_hours' => substr($Slot->wrk_work_hours, 0, 2) . ':' . substr($Slot->wrk_work_hours, 3, 2),
                ];
                $clientworktype = $this->PossibleWorkTypeRecords[$Slot->wt_cd];
                $this->calcSlot($dayIndex, $slotNo, $clientworktype->wt_pay_std, $clientworktype->wt_bill_std);
            }
            $dayIndex++;
        }
    }

    /**
     * mount function
     * */
    // public function mount()
    public function mount($workYear, $workMonth, $client_id, $clientplace_id, $employee_id)
    {
        parent::mount($workYear, $workMonth, $client_id, $clientplace_id, $employee_id);
        
        $this->PossibleWorkTypeRecords = modelClientWorktypes::possibleWorkTypeRecords($this->client_id, $this->clientplace_id);

        $this->clearData(self::MAX_SLOTS);         // 勤怠データ変数をクリアする
        $this->clearSummary();      // 集計をクリアする
        $this->fillTimekeepings();  // 勤怠データを読み込む

        // スロットの背景色を設定する
        $this->SlotBGColors = [
            1 => 0,
            2 => 1,
            3 => 0,
            4 => 1,
            5 => 0,
            6 => 1,
            7 => 0,
        ];

        // 作業種別の選択肢を作成する
        $this->WorkTypes[''] = '';
        foreach($this->PossibleWorkTypeRecords as $wt_cd => $Record)
        {
            $this->WorkTypes[$wt_cd] = $Record->wt_name;
        }
    }

    public function render()
    {
        return view('livewire.employeeworksslot');
    }

    /**
     * 有給フラグの変更
     * param bool $value 有給フラグ, int $day 日
     * return void
     */
    public function leaveChange($value, $day)
    {
        $this->TimekeepingDays[$day]['leave'] = $value;
        for($slotNo=1; $slotNo<=self::MAX_SLOTS; $slotNo++)
        {
            $this->TimekeepingSlots[$day][$slotNo]['class_bg_color'] = $value ? 'bg-gray-100' : '';
            $this->TimekeepingSlots[$day][$slotNo]['readonly'] = $value ? 'readonly=\"readonly\"' : '';
        }
    }

    /**
     * 作業種別の変更
     * @param string $value 作業種別, int $day 日, int $slot スロット番号 >= 1
     */
    public function workTypeChange($value, $slot) : void
    {
        // エラーメッセージをリセット
        $this->resetErrorBag('TimekeepingTypes.' . $slot . '.wt_cd');

        // 正しい作業種別かどうかをチェック
        $value = trim($value);
        if(! array_key_exists($value, $this->WorkTypes))
        {
            $this->addError('TimekeepingTypes.' . $slot . '.wt_cd', '作業CD');
            return;
        }

        // 作業種別をクリア
        if($value == '')
        {
            $this->clearWorkType($slot);
        }
        else
        {
            // 作業種別を設定
            $this->setWorkType($slot, $value);
        }
    }

    /**
     * log start time change
     * */
    public function logStartTimeChange($value, $day, $slot)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slot . '.wrk_log_start';

        // 作業種別レコードを取得
        $wt_cd = $this->TimekeepingTypes[$slot];
        if($wt_cd == '')
        {   // 作業種別が未設定の場合は時間計算しない
            $this->addError($item, '作業種別');
            return;
        }
        $clientworktype = $this->PossibleWorkTypeRecords[$wt_cd];

        // チェックを実行
        try {
            // エラーメッセージをリセット
            $this->resetErrorBag($item);
            $value = $this->rukuruUtilTimeNormalize($value);
        } catch (\Exception $e) {
            $this->addError($item, $e->getMessage());
            return;
        }

        $this->TimekeepingSlots[$day][$slot]['wrk_log_start'] = $value;
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = '';


        // 時間計算用のクラスインスタンスを作成
        $Slot = new TimeSlotSlot(
            $this->TimekeepingDays[$day]['DateTime'],
            "05:00", 
            intval($slotNo),
            $this->Client, 
            $clientworktype, 
            $this->TimekeepingSlots[$day][$slot]['wrk_log_start'],
            $this->TimekeepingSlots[$day][$slot]['wrk_log_end']
        );

        // 作業時間を計算
        $this->TimekeepingSlots[$day][$slot]['wrk_work_start'] = $Slot->getWorkStart();
        $this->TimekeepingSlots[$day][$slot]['wrk_work_end'] = $Slot->getWorkEnd();
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = $Slot->getWorkHours();

        // 集計作業
        $this->calcSlot($day, $slot, $clientworktype->wt_pay_std, $clientworktype->wt_bill_std);
    }

    /**
     * log end time change
     * */
    public function logEndTimeChange($value, $day, $slot)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slot . '.wrk_log_end';

        // 作業種別レコードを取得
        $wt_cd = $this->TimekeepingTypes[$slot];
        if($wt_cd == '')
        {   // 作業種別が未設定の場合は時間計算しない
            $this->addError($item, '作業種別');
            return;
        }
        $clientworktype = $this->PossibleWorkTypeRecords[$wt_cd];

        // チェックを実行
        try {
            // エラーメッセージをリセット
            $this->resetErrorBag($item);
            $value = $this->rukuruUtilTimeNormalize($value);
        } catch (\Exception $e) {
            $this->addError($item, $e->getMessage());
            return;
        }

        $this->TimekeepingSlots[$day][$slot]['wrk_log_end'] = $value;
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = '';
        
        // 時間計算用のクラスインスタンスを作成
        $Slot = new TimeSlotSlot(
            $this->TimekeepingDays[$day]['DateTime'],
            "05:00", 
            intval($slotNo),
            $this->Client, 
            $clientworktype, 
            $this->TimekeepingSlots[$day][$slot]['wrk_log_start'],
            $this->TimekeepingSlots[$day][$slot]['wrk_log_end']
        );

        // 作業時間を計算
        $this->TimekeepingSlots[$day][$slot]['wrk_work_start'] = $Slot->getWorkStart();
        $this->TimekeepingSlots[$day][$slot]['wrk_work_end'] = $Slot->getWorkEnd();
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = $Slot->getWorkHours();

        // 集計作業
        $this->calcSlot($day, $slot, $clientworktype->wt_pay_std, $clientworktype->wt_bill_std);
    }

    /**
     * save work time
     */
    protected function insertEmployeeWork()
    {
        foreach($this->TimekeepingSlots as $day => $Slots)
        {
            foreach($Slots as $slotNo => $Slot)
            {
                if(! $this->mustWriteSlot($day, $slotNo))
                {
                    continue;
                }

                // 有給の場合
                if(!empty($this->TimekeepingDays[$day]['leave']) && $slotNo == 1)
                {
                    $Work = new modelEmployeeWorks();
                    $Work->employee_id = $this->employee_id;
                    $Work->wrk_date = $this->TimekeepingDays[$day]['date'];
                    $Work->wrk_seq = $slotNo;
                    $Work->leave = $this->TimekeepingDays[$day]['leave'] ? 1 : null;
                    $Work->client_id = $this->client_id;
                    $Work->clientplace_id = $this->clientplace_id;
                    $Work->holiday_type = 0;
                    $Work->work_type = 1;
                    $Work->wt_cd = '';
                    $Work->wt_name = '有給';
    
                    $Work->wrk_log_start = null;
                    $Work->wrk_log_end = null;
                    $Work->wrk_work_start = null;
                    $Work->wrk_work_end = null;
                    $Work->wrk_break = null;
                    $Work->wrk_work_hours = null;
    
                    $Work->summary_index = 1;       // 1 で良いか？
                    $Work->summary_name = '有給';
                    
                    $Work->payhour = 0;
                    $Work->wrk_pay = $this->rukuruUtilMoneyValue($this->Employee->empl_paid_leave_pay, 0);
                    $Work->billhour = 0;
                    $Work->wrk_bill = 0;
    
                    $Work->notes = $this->TimekeepingDays[$day]['notes'];    
                    $Work->save();
                    continue;
                }

                $clientworktype = $this->PossibleWorkTypeRecords[$Slot['wt_cd']];
        
                $Work = new modelEmployeeWorks();
                $Work->employee_id = $this->employee_id;
                $Work->wrk_date = $this->TimekeepingDays[$day]['date'];
                $Work->wrk_seq = $slotNo;
                $Work->leave = ($slotNo == 1) ? $this->TimekeepingDays[$day]['leave'] : null;
                $Work->client_id = $this->client_id;
                $Work->clientplace_id = $this->clientplace_id;
                $Work->holiday_type = $this->TimekeepingDays[$day]['holiday_type'];
                $Work->work_type = $this->TimekeepingDays[$day]['work_type'];
                $Work->wt_cd = $this->TimekeepingTypes[$slotNo];
                $Work->wt_name = $clientworktype->wt_name;

                $Work->wrk_log_start = empty($Slot['wrk_log_start']) ? null : $Slot['wrk_log_start'];
                $Work->wrk_log_end = empty($Slot['wrk_log_end']) ? null : $Slot['wrk_log_end'];
                $Work->wrk_work_start = empty($Slot['wrk_work_start']) ? null : $Slot['wrk_work_start'];
                $Work->wrk_work_end = empty($Slot['wrk_work_end']) ? null : $Slot['wrk_work_end'];
                $Work->wrk_break = empty($Slot['wrk_break']) ? null : $Slot['wrk_break'];
                $Work->wrk_work_hours = empty($Slot['wrk_work_hours']) ? null : $Slot['wrk_work_hours'];

                $Work->summary_index = $slotNo;
                $Work->summary_name = $clientworktype->wt_name;
                
                $diSlotWorkHours = $this->rukuruUtilTimeToDateInterval(empty($Slot['wrk_work_hours']) ? '00:00' : $Slot['wrk_work_hours']);
                $Work->payhour = $this->rukuruUtilMoneyValue($clientworktype->wt_pay_std, 0);
                $Work->wrk_pay = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->payhour);
                $Work->billhour = $this->rukuruUtilMoneyValue($clientworktype->wt_bill_std, 0);
                $Work->wrk_bill = $this->rukuruUtilDateIntervalToMoney($diSlotWorkHours, $Work->billhour);

                $Work->notes = $this->TimekeepingDays[$day]['notes'];

                // 有給の項目設定

                $Work->save();
            }
        }
    }
}
