<?php

namespace App\Livewire;

use DateTime;

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
class Employeeworksone extends Component
{
    use rukuruUtilities;
    
    #[Layout('layouts.app')]

    // constants
    private const MAX_SLOTS = 4;

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
     * possible work types
     */
    //public $PossibleWorkTypes;
    public $PossibleWorkTypeRecords;
    public $WorkTypes = [];

    /**
     * スロットの背景色
     */
    public $SlotBGColors = [];

    /**
     * timekeeping array
     */
    public $TimekeepingDays = [];       // information concerning the one day
    public $TimekeepingSlots = [];      // slots of the day

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
     * validation rules
     */
    protected $rules = [
    ];

    /**
     * 集計エリアをクリアする
     */
    protected function clearSummary()
    {
        $this->SumDayWeekdays = 0;            // 平日出勤日数
        $this->SumDayHolidays = 0;            // 休日出勤日数
        $this->SumDayHolidaysLegal = 0;       // 法定休日出勤日数
        
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
     * 勤怠読み込み
     */
    protected function fillTimekeepings()
    {
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($firstDate))));

        $this->SlotBGColors = [
            1 => 'bg-light',
            2 => 'bg-orange-500',
            3 => 'bg-light',
            4 => 'bg-orange-500',
            5 => 'bg-light',
            6 => 'bg-orange-500',
            7 => 'bg-light',
        ];

        for($day = 1; $day <= date('t', strtotime($firstDate)); $day++)
        {
            $DateCur = new DateTime($this->workYear . '-' . $this->workMonth . '-' . $day);
            $this->TimekeepingDays[$day]['DateTime'] = $DateCur;    // DateTime object
            $this->TimekeepingDays[$day]['day'] = $day;             // day of the month
            $this->TimekeepingDays[$day]['dispDayOfWeek'] = $dayOfWeek[$DateCur->format('w')];    // day of the week
            $this->TimekeepingDays[$day]['leave'] = false;          // 有給 flag
            $this->TimekeepingDays[$day]['holiday_type'] = 0;       // 休日区分 0:平日 1:法定休日 2:法定外休日
            $this->TimekeepingDays[$day]['work_type'] = 1;          // 勤務体系

            $curDate = $this->workYear . '-' . $this->workMonth . '-' . substr('0' . $day, -2);
            $this->TimekeepingDays[$day]['date'] = $curDate;

            // スロットデータ配列をクリアする
            for($slotNo=1; $slotNo<=self::MAX_SLOTS; $slotNo++)
            {
                $this->TimekeepingSlots[$day][$slotNo] = [
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
        $this->WorktypeRecords = modelWorktypes::where('worktype_kintai', 0)->orderBy('worktype_cd')->get();
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

        $this->fillTimekeepings();
    }

    public function render()
    {
        return view('livewire.employeeworksone');
    }
}
