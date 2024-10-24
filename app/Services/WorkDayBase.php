<?php

namespace App\Services;

use DateTime;
use Exception;

use App\Models\employeeworks as modelEmployeeworks;

/**
 * Class WorkDayBase
 * @package App\Services
 * 従業員の1日の勤怠基底クラス
 */
abstract class WorkDayBase
{
    /**
     * 日付
     */
    protected readonly DateTime $date;

    /**
     * 有給フラグ
     */
    protected int $leave;

    /**
     * 勤怠スロットの配列
     * @array WorkSlotBase[]
     */
    protected $workSlots;

    /**
     * 最後のスロットの終了時間
     * @var DateTime|null
     */
    protected ?DateTime $dtWorkEnd;

    /**
     * WorkDayBase constructor
     * @param protected WorkHoursBase $workHours
     * @param protected readonly int $day
     */
    public function __construct(
        protected WorkHoursBase $workHours,
        protected readonly int $day
    )
    {
        $this->date = new DateTime($this->workHours->getTargetYear() . '-' . $this->workHours->getTargetMonth() . '-' . $this->day);
        $this->leave = 0;
        $this->workSlots = [];
        $this->dtWorkEnd = null;
    }

    /**
     * 親の WorkHoursBase を取得
     */
    public function getWorkHours() : WorkHoursBase
    {
        return $this->workHours;
    }

    /**
     * 日付を取得
     * @return DateTime
     */
    public function getDate() : DateTime
    {
        return $this->date;
    }

    /**
     * 有給設定
     * @param int $leave
     */
    public function setLeave(int $leave) : void
    {
        $this->leave = $leave;
    }

    /**
     * バリデーション
     * @param DateTime|null $dtWorkEnd  直前の最終勤怠終了時間
     * @throws Exception
     */
    public function validate(?DateTime $dtWorkEnd) : void
    {
        // 勤怠スロットのバリデーション
        foreach ($this->workSlots as $workSlot) {
            $workSlot->validate($dtWorkEnd);
            $dtWorkEnd = $workSlot->getDtWorkEnd();
        }
    }

    /**
     * 最後のスロットの終了時間を取得
     */
    public function getLastWorkEnd() : ?DateTime
    {
        return $this->dtWorkEnd;
    }

    /**
     * DB読込
     * @param void
     */
    public function load() : void
    {
        // DB読込
        $EmployeeWorks = modelEmployeeworks::where('client_id', $this->workHours->getClientId())
            ->where('clientplace_id', $this->workHours->getClientplaceId())
            ->where('employee_id', $this->workHours->getEmployeeId())
            ->where('wrk_date', $this->date->format('Y-m-d'))
            ->orderBy('wrk_seq')
            ->get();
        if($EmployeeWorks->count() > 0)
        {
            $slotNo = 0;
            foreach($EmployeeWorks as $EmployeeWork)
            {
                // 最終スロットの終了時間を更新
                $dtWorkEnd = new DateTime($EmployeeWork->wrk_work_end);
                if($this->dtWorkEnd == null || $dtWorkEnd > $this->dtWorkEnd)
                {
                    $this->dtWorkEnd = $dtWorkEnd;
                }
                // スロットを作成
                $workSlot = new WorkSlotBase($this, $slotNo);
                $workSlot->load($EmployeeWork);
                $this->workSlots[] = $workSlot;
                $slotNo++;
            }
        }
    }

    /**
     * DB保存
     */
    public function save() : void
    {
        // DB読込
        $EmployeeWorks = modelEmployeeworks::where('client_id', $this->workHours->getClientId())
            ->where('clientplace_id', $this->workHours->getClientplaceId())
            ->where('employee_id', $this->workHours->getEmployeeId())
            ->where('wrk_date', $this->date->format('Y-m-d'))
            ->orderBy('wrk_seq')
            ->get();
        // DB保存
        $slotNo = 0;
        if($EmployeeWorks)
        {
            foreach($EmployeeWorks as $EmployeeWork)
            {
                // スロットを作成
                $workSlot = new WorkSlotBase($this, $slotNo);
                $workSlot->load($EmployeeWork);
                if($slotNo >= count($this->workSlots))
                {
                    $EmployeeWork->delete();
                }
                else
                {
                    if(! $workSlot->eq($this->workSlots[$slotNo]))
                    {
                        $this->workSlots[$slotNo]->update();
                    }
                }
                $slotNo++;
            }
        }
        if($slotNo < count($this->workSlots))
        {
            for($i = $slotNo; $i < count($this->workSlots); $i++)
            {
                $this->workSlots[$i]->save();
            }
        }
    }

    /**
     * スロット配列を取得
     * @return WorkSlotBase[]
     */
    public function getWorkSlots() : array
    {
        return $this->workSlots;
    }

    /**
     * スロットを取得
     * @param int $slot
     * @return WorkSlotBase
     * @comment 無かったら作成する
     */
    public function getWorkSlot(int $slot) : WorkSlotBase
    {
        if(! isset($this->workSlots[$slot]))
        {
            $this->workSlots[$slot] = new WorkSlotBase($this, $slot);
        }
        return $this->workSlots[$slot];
    }
}