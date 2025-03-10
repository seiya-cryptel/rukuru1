<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\worktype as modelWorktypes;
use App\Models\employees as modelEmployees;
use App\Models\employeeworks as modelEmployeeWorks;

/**
 * 勤怠入力画面標準
 */
abstract class EmployeeworksBase extends Component
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants

    // parameters
    public $workYear;
    public $workMonth;
    public $client_id;
    public $clientplace_id;
    public $employee_id;

    /**
     * client record
     * */
    public $Client;

    /**
     * client place record
     * */
    public $ClientPlace;

    /**
     * employee list
     */
    public $Employees = [];

    /**
     * employee record
     * */
    public $Employee;

    /**
     * 勤務体系
     */
    public $KinmuTaikeies = [];

    /**
     * 従業員選択用id
     */
    public $nextEmployeeId;

    /**
     * timekeeping array
     */
    public $TimekeepingDays = [];   // 日にちごとの情報
                                    // ['DateTime' => DateTime object, 'day' => 日, 'dispDayOfWeek' => 曜日, 'leave' => 有給, 'holiday_type' => 休日区分, 'work_type' => 勤務体系, 'date' => 日付]
    public $TimekeepingSlots = [];      // slots of the day

    // 計算用
    public $HoursSlotDay = [];    // 集計スロット [$slotSum] ごと、日にち [$dayIndex] ごとの作業時間
    public $PaySlotDay = [];        // 集計スロット [$slotSum] ごと、日にち [$dayIndex] ごとの支給額
    public $BillSlotDay = [];       // 集計スロット [$slotSum] ごと、日にち [$dayIndex] ごとの請求額

    /**
     * 集計表示用
     */
    public $SumWorkHours = [];       // スロットごとの作業時間の合計
    public $SumWorkHoursAll = '0:00';  // 1ヶ月の就業時間の合計
    public $SumWorkPays = [];        // スロットごとの支給額の合計
    public $SumWorkPayAll = 0;   //  1ヶ月の支給額の合計
    public $SumWorkBills = [];       // スロットごとの請求額の合計
    public $SumWorkBillAll = 0;   // 1ヶ月の請求額の合計

    public $SumWorkTypes = [];      // wt_name: 作業種別名, wt_pay: 時給, wt_bill: 請求額

    /**
     * validation rules
     */
    protected $rules = [
    ];

    /**
     * 勤怠タイプ
     */
    abstract protected function worktypeKintai() : int;

    /**
     * 集計エリアをクリアする
     */
    abstract protected function clearSummary();

    /**
     * 1日の作業時間合計を計算
     * @param $dayIndex 日
     * @param $slotMax スロット数
     * @return dateInterval 作業時間合計
     */
    protected function sumDayWorkHours($dayIndex, $slotMax)
    {
        $dayWorkHours = new DateInterval('P0D');  // DateInterval 作業時間合計／日
        for($slotNo = 1; $slotNo <= $slotMax; $slotNo++)
        {
            if(!empty($this->TimekeepingSlots[$dayIndex][$slotNo]['wrk_work_hours']))
            {
                $workHours = $this->rukuruUtilTimeToDateInterval($this->TimekeepingSlots[$dayIndex][$slotNo]['wrk_work_hours']);
                $dayWorkHours = $this->rukuruUtilDateIntervalAdd($dayWorkHours, $workHours);
            }
        }
        return $dayWorkHours;
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
     * データエリアをクリアする
     * @param $maxSlot 最大スロット数
     */
    protected function clearData($maxSlot)
    {
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

        // 勤怠の開始日と終了日を計算する
        $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $this->Client->cl_close_day);
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        $this->TimekeepingDays = [];    // 日ごとの情報
        $this->TimekeepingSlots = [];   // 日ごと、スロットごとの情報

        // 日にちごとの情報をクリアする
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            // 日ごとの情報を設定する
            $this->TimekeepingDays[$dayIndex] = [
                'DateTime' => new DateTime(date('Y-m-d', $dt)),    // DateTime object
                'day' => date('j', $dt),             // day of the month
                'dispDayOfWeek' => $dayOfWeek[date('w', $dt)],    // day of the week
                'leave' => 0,              // 有給休暇 0: なし 1: 有給休暇, 2: 特別休暇
                'holiday_type' => 0,       // 休日区分 0:平日 1:法定休日 2:法定外休日
                'work_type' => 1,          // 勤務体系
                'date' => date('Y-m-d', $dt),  // date
                'wrk_leave_hour1' => '',   // 有給休暇時間
                'wrk_leave_hour2' => '',   // 特別休暇時間
                'notes' => '',             // 備考
            ];
            $dayIndex++;
        }
        // 日にちスロットごとの情報をクリアする
        $dayIndex = 1;
        for($dt = $dtFirstDate; $dt <= $dtLastDate; $dt = strtotime('+1 day', $dt))
        {
            for($slotNo=1; $slotNo<=$maxSlot; $slotNo++)
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
     * mount function
     * */
    public function mount($workYear, $workMonth, $client_id, $clientplace_id, $employee_id)
    {
        $this->workYear = $workYear;
        $this->workMonth = $workMonth;
        $this->client_id = $client_id;
        $this->clientplace_id = $clientplace_id;
        $this->employee_id = $employee_id;
        $this->nextEmployeeId = $employee_id;
        $this->Client = modelClients::find($this->client_id);
        $this->ClientPlace = $this->clientplace_id ? modelClientPlaces::find($this->clientplace_id) : null;
        $this->Employee = modelEmployees::find($this->employee_id);
        $this->Employees = modelEmployees::orderBy('empl_cd')
            ->get();

        // 勤務体系の配列を作る
        $workTypeRecords = modelWorktypes::where('worktype_kintai', $this->worktypeKintai())
            ->orderBy('worktype_cd')
            ->get();
        $this->WorktypeRecords = [];
        foreach($workTypeRecords as $workTypeRecord)
        {
            $this->WorktypeRecords[$workTypeRecord->worktype_cd] = $workTypeRecord;
        }

        // 勤務体系の選択肢を作成する。
        $this->KinmuTaikeies = [];
        foreach($this->WorktypeRecords as $WorktypeReccord)
        {
            $this->KinmuTaikeies[$WorktypeReccord->worktype_cd] = $WorktypeReccord->worktype_name;
        }
    }

    abstract public function render();

    /**
     * log start time change
     * @param $value 変更値
     * @param $day 日
     * @param $slotNo スロット番号
     * */
    abstract public function logStartTimeChange($value, $day, $slotNo);

    /**
     * log end time change
     * @param $value 変更値
     * @param $day 日
     * @param $slotNo スロット番号
     * */
    abstract public function logEndTimeChange($value, $day, $slotNo);

    /**
     * 従業員が変更された
     */
    public function employeeChanged($nextEmployeeId)
    {
        // 勤怠を保存する
        $this->saveEmployeeWork();
        // 新しい勤怠入力画面に移動する
        $Client = modelClients::find($this->client_id);
        $route = $Client->cl_kintai_style == 0 ? 'employeeworksone' : 'employeeworksslot';
        return redirect()->route($route, [
            'workYear' => $this->workYear, 
            'workMonth' => $this->workMonth, 
            'clientId' => $this->client_id, 
            'clientPlaceId' => $this->clientplace_id, 
            'employeeId' => $nextEmployeeId,
        ]);
    }

    /**
     * delete work time by employee id and work year and work month
     */
    protected function deleteEmployeeWork()
    {
        // 勤怠の開始日と終了日を計算する
        $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $this->Client->cl_close_day);
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
    abstract protected function insertEmployeeWork();

    /**
     * save work time
     */
    public function saveEmployeeWork()
    {
        DB::beginTransaction();
        try {
            $this->deleteEmployeeWork();
            $this->insertEmployeeWork();
            // $this->makeSalary();
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
