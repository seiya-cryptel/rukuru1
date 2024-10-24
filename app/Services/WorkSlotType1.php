<?php

namespace App\Services;

use App\Traits\rukuruUtilites;

class WorkSlotType1 extends WorkSlotBase
{
    use rukuruUtilites;

    /**
     * 開始打刻設定
     * @param string $log_start
     * @throws Exception
     */
    public function setLogStart(string $log_start) : void
    {
        try{
            $this->log_start = $this->rukuruUtilTimeNormalize($log_start);
        }catch(Exception $e){
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':開始打刻書式が不正です。');
        }
        // 開始時刻の設定
        $dtLogStart = new DateTime($this->log_start);
        if($dtLogStart < $this->wt_start)
        {
            $this->work_start = $this->wt_start;    // 勤務開始時刻より前の場合は勤務開始時刻を設定
        }
        elseif($dtLogStart > $this->wt_start)
        {
            $this->work_start = $this->rukuruUtilTimeRoundUp($dtLogStart, 15);        // 勤務開始時刻より後の場合はまるめた打刻時刻
        }
    }

    /**
     * 終了打刻設定
     * @param string $log_end
     * @throws Exception
     */
    public function setLogEnd(string $log_end) : void
    {
        try{
            $this->log_end = $this->rukuruUtilTimeNormalize($log_end);
        }catch(Exception $e){
            throw new Exception($this->workDay->format('m/d') . ':' . ($this->slotNo + 1) . ':終了打刻書式が不正です。');
        }
        // 終了時刻の設定
        $dtLogEnd = new DateTime($this->log_end);
        $this->work_end = $this->rukuruUtilTimeRoundDown($dtLogEnd, 5);        // 勤務開始時刻より後の場合はまるめた打刻時刻
    }
    
}