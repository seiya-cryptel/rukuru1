<?php

namespace App\Services;

use DateTime;
use DateInterval;
use Exception;

use App\Models\clients as modelClients;
use App\Models\clientworktypes as modelClientworktypes;
// use App\Models\employeeworks as modelEmployeeworks;

use App\Traits\rukuruUtilites;

/**
 * Class TimeSlotBase
 * @package App\Services
 * 従業員の1スロット単体の勤怠基底クラス
 */
abstract class TimeSlotBase
{
    use rukuruUtilites;

    /**
     * 開始時刻、終了時刻
     * @var DateTime|null
     */
    public ?DateTime $work_start, $work_end;

    /**
     * 終業時間
     * @var DateTime|null
     */
    public ?DateInterval $work_hours;

    /**
     * 開始時刻計算
     */
    protected abstract function setStartTime() : void;

    /**
     * 終業時刻計算
     */
    protected abstract function setEndTime() : void;

    /**
     * 就業時間計算
     */
    protected abstract function setWorkTime() : void;

    /**
     * TimeSlotBase constructor
     * @param protected int $slotNo >= 1 スロット番号
     * @param protected modelClientworktypes $ClientWorkType 作業種別レコード
     */
    public function __construct(
        protected DateTime $currentDate,
        protected int $slotNo,
        protected modelClients $Client,
        protected modelClientworktypes $ClientWorkType,
        protected ?string $log_start = null,
        protected ?string $log_end = null
        )
    {
        $this->work_start = null;
        $this->work_end = null;
        $this->log_start = $this->rukuruUtilTimeNormalize($this->log_start);
        $this->log_end = $this->rukuruUtilTimeNormalize($this->log_end);
        $this->setStartTime();
        $this->setEndTime();
        $this->setWorkTime();
    }

    /**
     * 作業種別レコード設定
     * @param modelClientworktypes $ClientWorkType
     * @throws Exception
     */
    public function setClientWorkType(modelClientworktypes $ClientWorkType) : void
    {
        $this->ClientWorkType = $ClientWorkType;
        $this->setStartTime();
        $this->setEndTime();
        $this->setWorkTime();
    }

    /**
     * 開始打刻設定
     * @param string $log_start
     * @throws Exception
     */
    public function setLogStart(string $log_start) : void
    {
        $this->log_start = $this->rukuruUtilTimeNormalize($log_start);
        $this->setStartTime();
        $this->setEndTime();
        $this->setWorkTime();
    }

    /**
     * 終了打刻設定
     * @param string $log_end
     * @throws Exception
     */
    public function setLogEnd(string $log_end) : void
    {
        $this->log_end = $this->rukuruUtilTimeNormalize($log_end);
        $this->setStartTime();
        $this->setEndTime();
        $this->setWorkTime();
    }

    /**
     * 作業時間文字列を取得
     */
    public function getWorkHours()
    {
        return $this->work_hours->format('%H:%I');
    }
}