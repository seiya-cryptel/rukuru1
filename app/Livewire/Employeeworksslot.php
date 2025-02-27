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
class Employeeworksslot extends Component
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants
    private const MAX_SLOTS = 7;

    // parameters
    public $workYear;
    public $workMonth;
    public $client_id;
    public $clientplace_id;
    public $employee_id;

    /**
     * client record
     * */
    public modelClients $Client;

    /**
     * client place record
     * */
    public $ClientPlace;

    /**
     * employee record
     * */
    public $Employee;

    /**
     * 勤務体系
     */
    public $WorktypeRecords;
    public $KinmuTaikeies = [];

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
    public $TimekeepingDays = [];   // 日にちごとの情報
                                    // ['DateTime' => DateTime object, 'day' => 日, 'dispDayOfWeek' => 曜日, 'leave' => 有給, 'holiday_type' => 休日区分, 'work_type' => 勤務体系, 'date' => 日付]
    public $TimekeepingTypes = [];  // スロットごとの情報
                                    // ['wt_cd' => 作業種別]
    public $TimekeepingSlots = [];  // 日にち、スロットごとの情報
                                    // ['wrk_seq' => スロット番号, 'wt_cd' => 作業種別, 'wrk_log_start' => ログイン開始, 'wrk_log_end' => ログイン終了, 'wrk_work_start' => 作業開始, 'wrk_work_end' => 作業終了, 'wrk_work_hours' => 作業時間, 'class_bg_color' => 背景色, 'readonly' => 読み取り専用]

    // 計算用
    public $DayHasWork = [];        // 日にち [$dayIndex] ごとの作業の有無
                                    // 0 作業なし, 1 作業あり
    public $HoursSlotDay = [];    // スロット [$slotNo] ごと、日にち [$dayIndex] ごとの作業時間
    public $PaySlotDay = [];        // スロット [$slotNo] ごと、日にち [$dayIndex] ごとの支給額
    public $BillSlotDay = [];       // スロット [$slotNo] ごと、日にち [$dayIndex] ごとの請求額

    /**
     * 集計表示用
     */
    public $SumDays;            // 日数

    public $SumWorkHours = [];       // 作業時間計算用 スロットごとの作業時間の合計
    public $SumWorkHoursAll = '0:00';  // 作業時間計算用 1ヶ月の作業時間の合計
    public $SumWorkPays = [];        // 作業時間計算用 スロットごとの支給額の合計
    public $SumWorkPaysAll = 0;   // 作業時間計算用 1ヶ月の支給額の合計
    public $SumWorkBills = [];       // 作業時間計算用 スロットごとの請求額の合計
    public $SumWorkBillsAll = 0;   // 作業時間計算用 1ヶ月の請求額の合計

    public $SumWorkTypes = [];      // wt_name: 作業種別名, wt_pay: 時給, wt_bill: 請求額


    /**
     * 勤怠データ クラス変数
     * 2024/10/26 not primitive type variable makes trouble in Livewire component
     */
    // public $Workhours2;

    /**
     * validation rules
     */
    protected $rules = [
    ];

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
        $this->SumWorkHours = array_fill(1, self::MAX_SLOTS, null);  // 作業時間合計
        $this->SumWorkHoursAll = '0:00';  // 1ヶ月の作業時間合計
        $this->SumWorkPays = array_fill(1, self::MAX_SLOTS, 0);  // 支給額合計
        $this->SumWorkPaysAll = 0;   // 1ヶ月の支給額合計
        $this->SumWorkBills = array_fill(1, self::MAX_SLOTS, 0);  // 請求額合計
        $this->SumWorkBillsAll = 0;   // 1ヶ月の請求額合計

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
     * 1日の作業時間合計を計算
     * @param $day 日
     * @return dateInterval 作業時間合計
     */
    protected function sumDayWorkHours($day)
    {
        $dayWorkHours = $this->rukuruUtilTimeToDateInterval('00:00');  // DateInterval object
        for($i = 1; $i <= self::MAX_SLOTS; $i++)
        {
            if($this->TimekeepingSlots[$day][$i]['wrk_work_hours'] != '')
            {
                $workHours = $this->rukuruUtilTimeToDateInterval($this->TimekeepingSlots[$day][$i]['wrk_work_hours']);
                $dayWorkHours = $this->rukuruUtilDateIntervalAdd($dayWorkHours, $workHours);
            }
        }
        return $dayWorkHours;
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
        $dayWorkHours = $this->sumDayWorkHours($day);  // DateInterval object
        $hhmmDayWorkHours = $this->rukuruUtilDateIntervalFormat($dayWorkHours);

        // 作業日数合計
        $this->DayHasWork[$day] = ($hhmmDayWorkHours == '00:00') ? 0 : 1;   // 作業がある日は1、ない日は0
        $this->SumDays = array_sum($this->DayHasWork);

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
        $this->SumWorkPaysAll = array_sum($this->SumWorkPays);
        // 1ヶ月全スロットの請求額合計
        $this->SumWorkBillsAll = array_sum($this->SumWorkBills);
    }

    /**
     * データエリアをクリアする
     */
    protected function clearData()
    {
        // 勤怠の開始日と終了日を計算する
        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-' .  ($this->Client->cl_close_day + 1));
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        $this->TimekeepingDays = [];    // 日ごとの情報
        $this->TimekeepingTypes = [];   // スロットごとの情報
        $this->TimekeepingSlots = [];   // 日ごと、スロットごとの情報

        // 日にちごとの情報をクリアする
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            // 日ごとの情報を設定する
            $this->TimekeepingDays[$dayIndex] = [
                'DateTime' => new DateTime(date('Y-m-d', $dt)),    // DateTime object
                'day' => date('j', $dt),             // day of the month
                'dispDayOfWeek' => date('w', $dt),    // day of the week
                'leave' => false,          // 有給 flag
                'holiday_type' => 0,       // 休日区分 0:平日 1:法定休日 2:法定外休日
                'work_type' => 1,          // 勤務体系
                'date' => date('Y-m-d', $dt),  // date
                'notes' => '',             // 備考
            ];
            $dayIndex++;
        }
        // スロットごとの情報をクリアする
        for($slotNo=1; $slotNo<=self::MAX_SLOTS; $slotNo++)
        {
            $this->TimekeepingTypes[$slotNo] = '';
        }
        // 日にちスロットごとの情報をクリアする
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            for($slotNo=1; $slotNo<=self::MAX_SLOTS; $slotNo++)
            {
                $this->TimekeepingSlots[$dayIndex][$slotNo] = [
                    'wrk_seq' => $slotNo,
                    'wt_cd' => '',
                    'wt_name' => '',
                    'wrk_log_start' => '',
                    'wrk_log_end' => '',
                    'wrk_work_start' => '',
                    'wrk_work_end' => '',
                    'wrk_work_hours' => '',
                    'class_bg_color' => '',
                    'readonly' => '',
                ];
            }
            $dayIndex++;
        }
    }
    
    /**
     * 勤怠読み込み
     */
    protected function fillTimekeepings()
    {
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-' .  ($this->Client->cl_close_day + 1));
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        // スロットのデータを読み出す
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            $targetDate = date('Y-m-d', $dt);
            $Slots = modelEmployeeWorks::where('employee_id', $this->employee_id)
                ->where('wrk_date', $targetDate)
                ->orderBy('wrk_seq')
                ->get();

            foreach($Slots as $Slot)
            {
                $slotNo = $Slot->wrk_seq;
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
                    'class_bg_color' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'bg-gray-100' : '',
                    'readonly' => $this->TimekeepingDays[$dayIndex]['leave'] ? 'readonly=\"readonly\"' : '',
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
        $this->workYear = $workYear;
        $this->workMonth = $workMonth;
        $this->client_id = $client_id;
        $this->clientplace_id = $clientplace_id;
        $this->employee_id = $employee_id;
        $this->Client = modelClients::find($this->client_id);
        $this->ClientPlace = $this->clientplace_id ? modelClientPlaces::find($this->clientplace_id) : null;
        $this->Employee = modelEmployees::find($this->employee_id);
        $this->WorktypeRecords = modelWorktypes::where('worktype_kintai', 1)->orderBy('worktype_cd')->get();
        $this->PossibleWorkTypeRecords = modelClientWorktypes::possibleWorkTypeRecords($this->client_id, $this->clientplace_id);

        // 勤務体系の選択肢を作成する。
        $this->KinmuTaikeies = [];
        foreach($this->WorktypeRecords as $WorktypeReccord)
        {
            $this->KinmuTaikeies[$WorktypeReccord->worktype_cd] = $WorktypeReccord->worktype_name;
        }

        // 作業種別の選択肢を作成する
        $this->WorkTypes[''] = '';
        foreach($this->PossibleWorkTypeRecords as $wt_cd => $Record)
        {
            $this->WorkTypes[$wt_cd] = $Record->wt_name;
        }

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

        $this->clearData();         // 勤怠データ変数をクリアする
        $this->clearSummary();      // 集計をクリアする
        $this->fillTimekeepings();  // 勤怠データを読み込む

    }

    public function render()
    {
        return view('livewire.employeeworksslot');
    }

    /**
     * slot has data ?
     * param int $day 日, int $slot スロット
     * return bool true: データあり, false: データなし
     */
    protected function mustWriteSlot($day, $slot)
    {
        // 有給の場合は1スロット目のみ
        if($slot == 1 && $this->TimekeepingDays[$day]['leave'])
        {
            return true;
        }

        // 両方が空の場合はデータなし
        $log_start = empty($this->TimekeepingSlots[$day][$slot]['wrk_log_start']) ? null : $this->TimekeepingSlots[$day][$slot]['wrk_log_start'];
        $log_end = empty($this->TimekeepingSlots[$day][$slot]['wrk_log_end']) ? null : $this->TimekeepingSlots[$day][$slot]['wrk_log_end'];

        if($log_start == null && $log_end == null)
        {
            return false;
        }

        return true;
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
            $slot, 
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
            $slot, 
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
     * delete work time by employee id and work year and work month
     */
    protected function deleteEmployeeWork()
    {
        // 勤怠の開始日と終了日を計算する
        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-' .  ($this->Client->cl_close_day + 1));
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        $firstDay = date('Y-m-d', $dtFirstDate);
        $lastDay = date('Y-m-d', $dtLastDate);
        if(empty($this->clientplace_id))
        {
            modelEmployeeWorks::where('employee_id', $this->employee_id)
                ->where('client_id', $this->client_id)
                ->whereBetween('wrk_date',[$firstDay, $lastDay])
                ->delete();
        }
        else
        {
            modelEmployeeWorks::where('employee_id', $this->employee_id)
                ->where('client_id', $this->client_id)
                ->where('clientplace_id', $this->clientplace_id)
                ->whereBetween('wrk_date',[$firstDay, $lastDay])
                ->delete();
        }
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
                $Work->wrk_log_start = empty($Slot['wrk_log_start']) ? null : $Slot['wrk_log_start'];
                $Work->wrk_log_end = empty($Slot['wrk_log_end']) ? null : $Slot['wrk_log_end'];
                $Work->wrk_work_start = empty($Slot['wrk_work_start']) ? null : $Slot['wrk_work_start'];
                $Work->wrk_work_end = empty($Slot['wrk_work_end']) ? null : $Slot['wrk_work_end'];
                $Work->wrk_work_hours = empty($Slot['wrk_work_hours']) ? null : $Slot['wrk_work_hours'];
                $Work->notes = $this->TimekeepingDays[$day]['notes'];
                $Work->save();
            }
        }
    }

    /**
     * 従業員支給額レコードの作成、更新
     */
    protected function makeSalary()
    {
        // 従業員ID、対象年月から給与情報を作成または再作成
        $salary = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        if(!$salary) {
            $salary = new modelSalary();
            $salary->employee_id = $this->employee_id;
            $salary->work_year = $this->workYear;
            $salary->work_month = $this->workMonth;
            $salary->Transport = 0;
            $salary->allow_amount = 0;
            $salary->deduct_amount = 0;
        }

        // 給与情報を更新
        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $salary->work_amount = modelEmployeeSalarys::where('employee_id', $this->employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->sum('wrk_pay');
        $salary->notes = '';

        $salary->pay_amount = $salary->work_amount + $salary->allow_amount - $salary->deduct_amount + $salary->Transport;
        $salary->save();
    }

    /**
     * save work time
     */
    public function saveEmployeeWork()
    {
        DB::beginTransaction();
        try {
            $this->deleteEmployeeWork();
            $this->insertEmployeeWork();
            $this->makeSalary();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        session()->flash('success', __('Timekeeping') . ' ' . __('Saved.'));
        return $this->cancelEmployeepay();
    }

    /**
     * cancel work time
     */
    public function cancelEmployeepay()
    {
        // redirect to workemployees
        return redirect()->route('workemployee');
    }
}
