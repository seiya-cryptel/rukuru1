<?php

namespace App\Livewire;

use DateTime;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;
use App\Services\TimeSlotType1;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;
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
     * possible work types
     */
    //public $PossibleWorkTypes;
    public $PossibleWorkTypeRecords;
    public $WorkTypes = [];

    /**
     * timekeeping array
     */
    public $TimekeepingDays = [];       // information concerning the one day
    public $TimekeepingSlots = [];      // slots of the day

    /**
     * 勤怠データ クラス変数
     * 2024/10/26 not primitive type variable makes trouble in Livewire component
     */
    public $Workhours2;

    /**
     * validation rules
     */
    protected $rules = [
    ];

    /**
     * 勤怠読み込み
     */
    protected function fillTimekeepings()
    {
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($firstDate))));

        for($day = 1; $day <= date('t', strtotime($firstDate)); $day++)
        {
            $DateCur = new DateTime($this->workYear . '-' . $this->workMonth . '-' . $day);
            $this->TimekeepingDays[$day]['DateTime'] = $DateCur;    // DateTime object
            $this->TimekeepingDays[$day]['day'] = $day;             // day of the month
            $this->TimekeepingDays[$day]['dispDayOfWeek'] = $dayOfWeek[$DateCur->format('w')];    // day of the week
            $this->TimekeepingDays[$day]['leave'] = false;          // 有給 flag

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
        $this->ClientPlace = modelClientPlaces::find($this->clientplace_id);
        $this->Employee = modelEmployees::find($this->employee_id);
        $this->PossibleWorkTypeRecords = modelClientWorktypes::possibleWorkTypeRecords($this->client_id, $this->clientplace_id);
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
        return view('livewire.employeeworksslot');
    }
}
