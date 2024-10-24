<?php

namespace App\Services;

use DateTime;
use DB; // LaravelのDBファサードを使用
use Exception;

use App\Models\clients as modelClients;
use App\Models\employeeworks as modelEmployeeworks;

/**
 * Class WorkhoursBase
 * @package App\Services
 * 従業員の勤怠時間を計算する基底クラス
 */
abstract class WorkhoursBase
{
    /**
     * 顧客レコード
     */
    protected modelClients $Client;

    /**
     * １日の勤怠の配列
     * @array WorkDayBase[]
     */
    protected $workDays;

    /**
     * WorkhoursBase constructor
     * @param protected readonly int $client_id
     * @param protected readonly int $clientplace_id
     * @param protected readonly int $targetYear
     * @param protected readonly int $targetMonth
     * @param protected readonly int $employee_id
     */
    public function __construct(
        protected readonly int $client_id,
        protected readonly int $clientplace_id,
        protected readonly int $targetYear,
        protected readonly int $targetMonth,
        protected readonly int $employee_id
    )
    {
        $this->Client = modelClients::find($client_id);
        $this->workDays = [];
    }

    abstract protected function createWorkDay(int $day) : WorkDayBase;

    /**
     * バリデーション
     * @param void
     * @throws Exception
     */
    public function validate() : void
    {
        // 当月より前の最後のスロットを取得
        $previousWorkData = modelEmployeeworks::getPreviousWorkData(
            $this->client_id,
            $this->clientplace_id,
            $this->employee_id,
            $this->targetYear,
            $this->targetMonth
        );

        $dtWorkEnd = $previousWorkData ? new DateTime($previousWorkData->wrk_work_end) : null;

        $errorMessages = [];    // バリデーション エラーメッセージの配列

        // 開始時刻が前のスロットの終了時刻より前の場合はエラー
        foreach($this->workDays as $workDay)
        {
            $dtWorkEndOfDay = $workDay->getLastWorkEnd();   // 対象日の最後スロットの終了時刻
            try
            {
                $workDay->validate($dtWorkEnd);
            }
            catch(Exception $e)
            {
                $errorMessages[] = $e->getMessage();
            }
            // 対象日の最後スロットの終了時刻を更新
            if(dtWorkEndOfDay)
            {
                $dtWorkEnd = $dtWorkEndOfDay;
            }
        }
        if(! empty($errorMessages))
        {
            throw new Exception(implode("\n", $errorMessages));
        }
    }

    /**
     * DB読込
     * @param void
     */
    public function load() : void
    {
        $date = new DateTime();
        $date->setDate($this->targetYear, $this->targetMonth, 1);
        $lastDay = $date->format('t');
        for($day = 1; $day <= $lastDay; $day++)
        {
            $workDay = $this->createWorkDay($day);
            $workDay->load();
            $this->workDays[$day] = $workDay;
        }
    }

    /**
     * DB保存
     * @param void
     * @throws Exception
     */
    public function save() : void
    {
        db::beginTransaction();
        try{
            // 削除
            /* ちょっと乱暴なのでコメントアウト
            modelEmployeeworks::deleteWorkData(
                $this->client_id,
                $this->clientplace_id,
                $this->employee_id,
                $this->targetYear,
                $this->targetMonth
            );
            */
            // 追加
            foreach($this->workDays as $workDay)
            {
                $workDay->save();
            }
            db::commit();
        }
        catch(Exception $e)
        {
            db::rollBack();
            throw $e;
        }
    }

    /**
     * 指定日の勤怠データを取得
     * @param int $day
     * @return WorkDayBase | null
     */
    public function getWorkDay(int $day) : ?WorkDayBase
    {
        return $this->workDays[$day] ?? null;
    }

    /**
     * 指定日、指定スロットの勤怠データを取得
     * @param int $day
     * @param int $slot
     * @return WorkSlotBase
     * @comment スロットオブジェクトがなかったら作成する
     */
    public function getWorkSlot(int $day, int $slot) : WorkSlotBase
    {
        $workDay = $this->getWorkDay($day);
        if(! $workDay)
        {
            $workDay = $this->createWorkDay($day);
            $this->workDays[$day] = $workDay;
        }
        return $workDay->getWorkSlot($slot);
    }

    /**
     * 顧客IDを取得
     * @return int
     */
    public function getClientId() : int
    {
        return $this->client_id;
    }

    /**
     * 顧客施設IDを取得
     * @return int
     */
    public function getClientplaceId() : int
    {
        return $this->clientplace_id;
    }

    /**
     * 対象年を取得
     * @return int
     */
    public function getTargetYear() : int
    {
        return $this->targetYear;
    }

    /**
     * 対象月を取得
     * @return int
     */
    public function getTargetMonth() : int
    {
        return $this->targetMonth;
    }

    /**
     * 従業員IDを取得
     * @return int
     */
    public function getEmployeeId() : int
    {
        return $this->employee_id;
    }

}