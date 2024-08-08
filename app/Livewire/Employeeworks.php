<?php

namespace App\Livewire;

use DateTime;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;
use App\Models\employees as modelEmployees;
use App\Models\employeeworks as modelEmployeeWorks;

class Employeeworks extends Component
{
    #[Layout('layouts.app')]

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
     * employee record
     * */
    public $Employee;

    /**
     * possible work types
     */
    public $PossibleWorkTypes;

    /**
     * timekeeping array
     */
    public $TimekeepingDays = [];       // information concerning the one day
    public $TimekeepingSlots = [];      // slots of the day

    /**
     * mount function
     * */
    public function mount()
    {
        // セッション変数から取得する
        $this->employee_id = session('employee_id');
        $this->workYear = session('workYear');
        $this->workMonth = session('workMonth');
        $this->client_id = session('client_id');
        $this->clientplace_id = session('clientplace_id');
        // セッション変数を削除する
        session()->forget('employee_id');
        session()->forget('workYear');
        session()->forget('workMonth');
        session()->forget('client_id');
        session()->forget('clientplace_id');

        $this->Client = modelClients::find($this->client_id);
        $this->ClientPlace = modelClientPlaces::find($this->clientplace_id);
        $this->Employee = modelEmployees::find($this->employee_id);
        $this->PossibleWorkTypes = modelClientWorktypes::possibleWorkTypes($this->client_id, $this->clientplace_id);

        $this->fillTimekeepings();
     }

    /**
     * render function
     * */
    public function render()
    {
        return view('livewire.employeeworks');
    }

    /**
     * regulate time format
     * param string $item 項目名, string $time 時刻文字列
     * rerurn string 正規化された時刻文字列
     *  null: えらーあり
     *  空文字: 何もしない
     */
    protected function regulateTime($item, $time)
    {
        // エラーメッセージをリセット
        $this->resetErrorBag($item);

        // 英数字と記号を半角に変換
        $time = trim(mb_convert_kana($time, 'as'));

        // 空文字の場合は何もしない
        if($time == '')
        {
            return '';
        }

        // 許可する文字列
        //   999 数字3桁は hmm として扱う
        //  9999 数字4桁は hhmm として扱う
        //  9:99 数字1桁:数字2桁は h:mm として扱う
        // 99:99 数字2桁:数字2桁は hh:mm として扱う
        // : の代わりに . も許可する
        if(preg_match('/^[0-9]{3}$/', $time))
        {
            $hour = substr($time, 0, 1);
            $minute = substr($time, 1, 2);
        }
        elseif(preg_match('/^[0-9]{4}$/', $time))
        {
            $hour = substr($time, 0, 2);
            $minute = substr($time, 2, 2);
        }
        elseif(preg_match('/^[0-9][:.][0-9]{2}$/', $time))
        {
            $hour = substr($time, 0, 1);
            $minute = substr($time, 2, 2);
        }
        elseif(preg_match('/^[0-9]{2}[:.][0-9]{2}$/', $time))
        {
            $hour = substr($time, 0, 2);
            $minute = substr($time, 3, 2);
        }
        else
        {
            $this->addError($item, '形式');
            return null;
        }

        // 時間の範囲チェック
        if($hour < 0 || $hour > 23)
        {
            $this->addError($item, '時');
            return null;
        }
        // 分の範囲チェック
        if($minute < 0 || $minute > 59)
        {
            $this->addError($item, '分');
            return null;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    /**
     * calculate work hours
     */
    protected function calcWorkHours($timeStart, $timeEnd)
    {
        $timeStart = new DateTime($timeStart);
        $timeEnd = new DateTime($timeEnd);

        $diff = $timeStart->diff($timeEnd);
        $hours = $diff->h;
        $minutes = $diff->i;

        return sprintf('%02d:%02d', $hours, $minutes);
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
     * log start time change
     * */
    public function logStartTimeChange($value, $day, $slot)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slot . '.wrk_log_start';

        // チェックを実行
        $value = $this->regulateTime($item, $value);
        if($value === null)
        {   // エラーがある場合は何もしない
            return;
        }

        $this->TimekeepingSlots[$day][$slot]['wrk_log_start'] = $value;
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = '';
        if(!empty($this->TimekeepingSlots[$day][$slot]['wrk_log_end']))
        {
            $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = $this->calcWorkHours($value, $this->TimekeepingSlots[$day][$slot]['wrk_log_end']);
        }
    }

    /**
     * log end time change
     * */
    public function logEndTimeChange($value, $day, $slot)
    {
        // 項目名
        $item = 'TimekeepingSlots.' . $day . '.' . $slot . '.wrk_log_end';

        // チェックを実行
        $value = $this->regulateTime($item, $value);
        if($value === null)
        {   // エラーがある場合は何もしない
            return;
        }

        $this->TimekeepingSlots[$day][$slot]['wrk_log_end'] = $value;
        $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = '';
        if(!empty($this->TimekeepingSlots[$day][$slot]['wrk_log_start']))
        {
            $this->TimekeepingSlots[$day][$slot]['wrk_work_hours'] = $this->calcWorkHours($this->TimekeepingSlots[$day][$slot]['wrk_log_start'], $value);
        }
    }

    /**
     * delete work time by employee id and work year and work month
     */
    protected function deleteEmployeeWork()
    {
        modelEmployeeWorks::where('employee_id', $this->employee_id)
            ->where('wrk_date', 'like', $this->workYear . '-' . $this->workMonth . '%')
            ->delete();
    }    

    /**
     * fill timekeeping array
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
            for($slotNo=1; $slotNo<=4; $slotNo++)
            {
                $this->TimekeepingSlots[$day][$slotNo] = [
                    'wrk_seq' => $slotNo,
                    'wt_cd' => 'N',
                    'wrk_log_start' => '',
                    'wrk_log_end' => '',
                    'wrk_work_start' => '',
                    'wrk_work_end' => '',
                    'wrk_work_hours' => '',
                ];
            }

            // スローっとデータを読み出す
            $targetDate = $this->workYear . '-' . $this->workMonth . '-' . substr('0' . $day, -2);
            $Slots = modelEmployeeWorks::where('employee_id', $this->employee_id)
                ->where('wrk_date', $targetDate)
                ->orderBy('wrk_seq')
                ->get();
            foreach($Slots as $Slot)
            {
                $slotNo = $Slot->wrk_seq;
                $this->TimekeepingSlots[$day][$slotNo] = [
                    'wrk_seq' => $slotNo,
                    'wt_cd' => $Slot->wt_cd,
                    'wrk_log_start' => $Slot->wrk_log_start,
                    'wrk_log_end' => $Slot->wrk_log_end,
                    'wrk_work_start' => $Slot->wrk_work_start,
                    'wrk_work_end' => $Slot->wrk_work_end,
                    'wrk_work_hours' => $Slot->wrk_work_hours,
                ];
            }
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
                if($this->mustWriteSlot($day, $slotNo) == false)
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
                $Work->wt_cd = empty($Slot['wt_cd']) ? 'N' : $Slot['wt_cd'];
                $Work->wrk_log_start = empty($Slot['wrk_log_start']) ? null : $Slot['wrk_log_start'];
                $Work->wrk_log_end = empty($Slot['wrk_log_end']) ? null : $Slot['wrk_log_end'];
                $Work->wrk_work_start = empty($Slot['wrk_work_start']) ? null : $Slot['wrk_work_start'];
                $Work->wrk_work_end = empty($Slot['wrk_work_end']) ? null : $Slot['wrk_work_end'];
                $Work->wrk_work_hours = empty($Slot['wrk_work_hours']) ? null : $Slot['wrk_work_hours'];
                $Work->save();
            }
        }
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        session()->flash('success', __('Timekeeping') . 'を保存しました。');
        return $this->cancelEmployeepay();
    }

    /**
     * cancel work time
     */
    public function cancelEmployeepay()
    {
        // セッション変数にキーを設定する
        session(['workYear' => $this->workYear]);
        session(['workMonth' => $this->workMonth]);
        session(['client_id' => $this->client_id]);
        session(['clientplace_id' => $this->clientplace_id]);

        // redirect to workemployees
        return redirect()->route('workemployee');
    }
}
