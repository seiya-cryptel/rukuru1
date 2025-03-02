<?php

namespace App\Services;

use DateTime;
use Exception;

use App\Models\clientworktypes as modelClientworktypes;

use App\Traits\rukuruUtilities;

/**
 * Class WorkSlotBase
 * @package App\Services
 * 従業員の1スロットの勤怠基底クラス
 */
abstract class WorkSlotBase
{
    use rukuruUtilities;

    /**
     * 親の勤怠
     * @var WorkhoursBase
     */
    protected WorkhoursBase $workHours;
    
    /**
     * 作業コード
     * @var string|null
     */
    protected ?string $wt_cd;

    /**
     * 作業名
     * @var string|null
     */
    protected ?string $wt_name;

    /**
     * 始業時刻、終業時刻
     * @var DateTime|null
     */
    protected ?DateTime $wt_start, $wt_end;

    /**
     * 開始打刻、終了打刻
     * @var string|null
     */
    protected ?string $log_start, $log_end;

    /**
     * 開始時刻、終了時刻
     * @var DateTime|null
     */
    protected ?DateTime $work_start, $work_end;

    /**
     * 作
     * 終業時間
     * @var DateTime|null
     */
    protected ?DateTime $work_hours;

    /**
     * WorkSlotBase constructor
     * @param protected WorkDayBase $workDay
     * @param protected readonly int $slotNo
     */
    public function __construct(
        protected WorkDayBase $workDay,
        protected readonly int $slotNo
    )
    {
        $this->workhours = $this->workDay->getWorkHours();
        $this->wt_cd = null;
        $this->wt_name = null;
        $this->wt_start = null;
        $this->wt_end = null;
        $this->log_start = null;
        $this->log_end = null;
        $this->work_start = null;
        $this->work_end = null;
        $this->work_hours = null;
    }

    /**
     * 作業コード設定
     * @param string $wt_cd
     * @throws Exception
     */
    public function setWtCd(string $wt_cd) : void
    {
        $this->wt_cd = $wt_cd;
        $clientWorkType = modelClientworktypes::getSutable($this->workHours->getClientId(), $this->workHours->getClientplaceId(), $wt_cd);
        if(! $clientWorkType) {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':作業コードが不正です。');
        }
        $this->wt_name = $clientWorkType->wt_name;
        $this->wt_start = new DateTime($clientWorkType->wt_start);
        $this->wt_end = new DateTime($clientWorkType->wt_end);
    }

    /**
     * 開始打刻設定
     * @param string $log_start
     * @throws Exception
     */
    public function setLogStart(string $log_start) : void
    {
        $this->log_start = $this->rukuruUtilTimeNormalize($log_start);
    }

    /**
     * 終了打刻設定
     * @param string $log_end
     * @throws Exception
     */
    public function setLogEnd(string $log_end) : void
    {
        $this->log_end = $this->rukuruUtilTimeNormalize($log_end);
    }

    /**
     * 作業時間計算
     * @return 作業時間文字列
     * @throws Exception
     */
    public function getWorkHours() : string
    {
        if(empty($this->log_start) || empty($this->log_end))
        {
            return '';
        }
        $this->work_start = new DateTime($this->log_start);
        $this->work_end = new DateTime($this->log_end);
        if($this->work_start > $this->work_end)
        {
            $this->work_end->modify('+1 day');
        }
        $this->work_hours = $this->work_end->diff($this->work_start);
        return $this->work_hours->format('%H:%I');
    }

    /**
     * バリデーション
     * @param DateTime|null $dtWorkEnd  直前の最終勤怠終了時間
     * @throws Exception
     */
    public function validate(DateTime $dtWorkEnd) : void
    {
        if(empty($this->wt_cd))
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':作業コードが未設定です。');
        }
        if(empty($this->log_start))
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':開始打刻が未設定です。');
        }
        if(empty($this->log_end))
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':終了打刻が未設定です。');
        }
        if(empty($this->work_start))
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':開始時刻が計算できていません。');
        }
        if(empty($this->work_end))
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':終了時刻が計算できていません。');
        }
        if($this->work_start < $dtWorkEnd)
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':開始時刻が前の作業の終了時刻より前です。');
        }
        if($this->work_end <= $this->work_start)
        {
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':終了時刻が開始時刻より前です。');
        }
    }
}