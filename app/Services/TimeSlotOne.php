<?php

namespace App\Services;

use DateTime;
use DateInterval;
use Exception;

use App\Traits\rukuruUtilities;

use App\Models\clients as modelClients;
use App\Models\clientworktypes as modelClientworktypes;

/**
 * Class TimeSlotOne
 * @package App\Services
 * 1シフト／日タイプの勤怠計算クラス
 */
class TimeSlotOne extends TimeSlotBase
{
    use rukuruUtilities;

    /**
     * 開始時刻計算
     */
    protected function setStartTime() : void
    {
        // 開始打刻文字列が空なら開始時刻を null にする
        if($this->log_start == '')
        {
            $this->work_start = null;
            return;
        }
        // 文字列の時刻をDateTimeオブジェクトに変換
        try {
            $this->work_start = $this->rukuruUtilTimeToDateTime($this->currentDate, $this->log_start, $this->beginHourOfDay);
            if($this->Client->cl_round_start)
            {
                $this->rukuruUtilTimeRoundUp($this->work_start, $this->Client->cl_round_start);
            }
        }
        catch(Exception $e)
        {
            throw new \Exception('開始時刻が不正です');
        }
    }

    /**
     * 終業時刻計算
     */
    protected function setEndTime() : void
    {
        // 終了打刻文字列が空なら終了時刻を null にする
        if($this->log_end == '')
        {
            $this->work_end = null;
            return;
        }
        // 文字列の時刻をDateTimeオブジェクトに変換
        try {
            $this->work_end = $this->rukuruUtilTimeToDateTime($this->currentDate, $this->log_end, $this->beginHourOfDay);
            // work_start よりも後の日時となるよう日にちを加算
            if($this->work_start)
            {
                while($this->work_end < $this->work_start)
                {
                    $this->work_end->add(new DateInterval('P1D'));
                }
            }
            if($this->Client->cl_round_end)
            {
                $this->rukuruUtilTimeRoundDown($this->work_end, $this->Client->cl_round_end);
            }
        }
        catch(Exception $e)
        {
            throw new \Exception('終了時刻が不正です');
        }
    }

    /**
     * 就業時間計算
     */
    protected function setWorkTime() : void
    {
        if($this->work_start === null || $this->work_end === null)
        {
            // 時刻が設定されていない場合は計算しない
            $this->work_hours = null;
            return;
        }
        // 終業時刻が開始時刻より前の場合はエラー
        if($this->work_end < $this->work_start)
        {
            throw new \Exception('終業時刻が開始時刻より前です');
        }
        // 休憩時間
        $sBreak = '';
        switch($this->slotNo)
        {
            case 1:
                $sBreak = $this->ClientWorkType->wt_lunch_break;
                break;
            case 2:
                $sBreak = $this->ClientWorkType->wt_evening_break;
                break;
            case 3:
                $sBreak = $this->ClientWorkType->wt_night_break;
                break;
            case 4:
                $sBreak = $this->ClientWorkType->wt_midnight_break;
                break;
            default:
                break;
        }
        if(empty($sBreak))
        {
            $sBreak = '00:00';
        }
        try {
            $diBreak = $this->rukuruUtilTimeToDateInterval($sBreak);
            // 休憩時間差し引き前の就業時間
            $this->work_hours = $this->rukuruUtilWorkHours($this->currentDate, $this->work_start, $this->work_end, $this->ClientWorkType);
            // 休憩時間差し引き後の就業時間
            $this->work_hours = $this->rukuruUtilDateIntervalSub($this->work_hours, $diBreak);
        }
        catch(Exception $e)
        {
            throw new \Exception('作業時間の計算に失敗しました');
        }
    }

    /**
     * TimeSlotOne constructor
     */
    public function __construct(
        protected DateTime $currentDate,
        protected string $hhmmWorktypeTimeStart,
        protected int $slotNo,
        protected modelClients $Client,
        protected modelClientworktypes $ClientWorkType,
        protected ?string $log_start = null,
        protected ?string $log_end = null
        )
    {
        parent::__construct($currentDate, $hhmmWorktypeTimeStart, $slotNo, $Client, $ClientWorkType, $log_start, $log_end);
    }

    /**
     * 作業種別レコード設定
     * @param modelClientworktypes $ClientWorkType
     * @throws Exception
     */
    public function setClientWorkType(modelClientworktypes $ClientWorkType) : void
    {
        // 作業種別レコードが空なら例外を投げる
        if(!$ClientWorkType)
        {
            throw new \Exception('作業種別が空です');
        }
        // 作業種別レコードを設定
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
}