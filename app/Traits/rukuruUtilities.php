<?php

namespace App\Traits;

use DateTime;
use DateInterval;

use Illuminate\Validation\ValidationException;

use App\Models\clients as modelClients;
use App\Models\holiday;
use App\Models\employeepays as modelEmployeePays;

/**
 * Trait rukuruUtilities
 * @package App\Traits
 * ツール関数をまとめたTrait
 */
trait rukuruUtilities
{
    /**
     * 引数が empty なら null を、そうでなければそのまま返す
     */
    public function rukuruUtilEmptyToNull($value)
    {
        return empty($value) ? null : $value;
    }

    /**
     * DateInterval 型同士の足し算
     * @param DateInterval $a
     * @param DateInterval $b
     * @return DateInterval $a に $b を足した結果
     */
    public function rukuruUtilDateIntervalAdd($a, $b) : DateInterval
    {
        $date = new DateTime('00:00');
        $date->add($a);
        $date->add($b);
        return (new DateTime('00:00'))->diff($date);
    }

    /**
     * DateInterval 型同士の引き算
     * @param DateInterval $a
     * @param DateInterval $b
     * @return DateInterval $a から $b を引いた結果
     */
    public function rukuruUtilDateIntervalSub($a, $b) : DateInterval
    {
        $date = new DateTime('00:00');
        $date->add($a);
        $date->sub($b);
        return (new DateTime('00:00'))->diff($date);
    }

    /**
     * DateInterval に単価をかけて金額を計算する
     * @param DateInterval $interval
     * @param float $unit_price
     * @return integer 金額
     */
    public function rukuruUtilDateIntervalToMoney($interval, $unit_price) : int
    {
        $hours = $interval->h + ($interval->d * 24);
        $minutes = $interval->i;
        return $hours * $unit_price + round($minutes * $unit_price / 60, 0);
    }

    /**
     * 入力された時刻を正規化する
     * @param string $time
     * throw Exception
     */
    public function rukuruUtilTimeNormalize($time) : string
    {
        // 英数字と記号を半角に変換
        $time = trim(mb_convert_kana($time, 'as'));

        // 空文字の場合は何もしない 正常
        if($time == '')
        {
            return '';
        }

        // 許可する文字列
        //   999 数字3桁は hmm として扱う
        //  9999 数字4桁は hhmm として扱う
        //  9:99 数字1桁:数字2桁は h:mm として扱う
        // 99:99 数字2桁:数字2桁は hh:mm として扱う
        // : の代わりに . や , も許可する

        // 時間と分に分割する
        if(preg_match('/^[0-9]{3}$/', $time))   // 999
        {
            $hour = substr($time, 0, 1);
            $minute = substr($time, 1, 2);
        }
        elseif(preg_match('/^[0-9]{4}$/', $time))   // 9999
        {
            $hour = substr($time, 0, 2);
            $minute = substr($time, 2, 2);
        }
        elseif(preg_match('/^[0-9][:.\,][0-9]{2}$/', $time))    // 9:99
        {
            $hour = substr($time, 0, 1);
            $minute = substr($time, 2, 2);
        }
        elseif(preg_match('/^[0-9]{2}[:.\,][0-9]{2}$/', $time)) // 99:99
        {
            $hour = substr($time, 0, 2);
            $minute = substr($time, 3, 2);
        }
        else
        {
            throw new \Exception('形式');
        }

        // 時間の範囲チェック
        if($hour < 0 || $hour > 23)
        {
            throw new \Exception('時');
        }
        // 分の範囲チェック
        if($minute < 0 || $minute > 59)
        {
            throw new \Exception('分');
        }

        // 時間と分を結合して返す
        return sprintf('%02d:%02d', $hour, $minute);
    }

    /**
     * 文字列の時刻をDateTimeオブジェクトに変換する
     * @param DateTime $date 日付
     * @param string $time 時刻
     * @return DateTime
     * 5 時より前の場合は翌日として扱う
     * 時刻は正規化されているものとする
     */
    public function rukuruUtilTimeToDateTime($date, $time) : DateTime
    {
        // 時刻を DateTime オブジェクトに変換する
        $datetime = new DateTime($date->format('Y-m-d') . ' ' . $time);

        // 5 時より前の場合は翌日として扱う
        if($datetime->format('H') < 5)
        {
            $datetime->add(new DateInterval('P1D'));
        }

        return $datetime;
    }

    /**
     * hh:mm, hh:mm:ss 文字列を DateInterval に変換する
     * @param string $time
     * @return DateInterval
     */
    public function rukuruUtilTimeToDateInterval($time) : DateInterval
    {
        // 時刻を DateInterval に変換する
        return new DateInterval('PT' . substr($time, 0, 2) . 'H' . substr($time, 3, 2) . 'M');
    }

    /**
     * DateInterval を hh:mm 形式の文字列に変換する
     * @param DateInterval $interval
     * @return string
     */
    public function rukuruUtilDateIntervalFormat($interval) : string
    {
        if(!$interval)
        {
            return '';
        }
        $hours = $interval->h + ($interval->d * 24);
        $minutes = $interval->i;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * 開始時刻をまるめる
     * @param DateTime $time
     * @param integer $minutes
     * @return DateTime
     */
    public function rukuruUtilTimeRoundUp($time, $minutes)
    {
        if($minutes < 1)
        {
            return $time;
        }
        $min = $time->format('i');
        $min = ceil($min / $minutes) * $minutes;
        $time->setTime($time->format('H'), $min); // $timeの分の値を$minに変更する
        return $time;
    }

    /**
     * 終了時刻をまるめる
     * @param DateTime $time
     * @param integer $minutes
     * @return DateTime
     */
    public function rukuruUtilTimeRoundDown($time, $minutes)
    {
        if($minutes < 1)
        {
            return $time;
        }
        $min = $time->format('i');
        $min = floor($min / $minutes) * $minutes;
        $time->setTime($time->format('H'), $min); // $timeの分の値を$minに変更する
        return $time;
    }

    /**
     * 休憩時間を考慮した就業時間を計算する
     * @param DateTime $currentDate 日付
     * @param DateTime $start 開始時刻
     * @param DateTime $end 終了時刻
     * @param modelClientworktypes $ClientWorkType 作業種別レコード
     * @return DateInterval
     * @throws Exception
     * 終了時刻は日替を考慮しているものとする
     */
    // 2.1. 日勤 a) 休憩時刻が決められている場合
    public function rukuruUtilWorkHoursDayBreakTime($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // 休憩時間を 考慮しない就業時間
        $work_hours = $start->diff($end);   // DateInterval

        // 作業種別の開始終業時刻をDateTimeに変換する
        $wt_work_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_work_start); // DateTime
        $wt_work_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_work_end); // DateTime
        if($wt_work_end < $wt_work_start)
        {
            $wt_work_end->add(new DateInterval('P1D'));
        }

        // 開始時刻が終業時刻より後の場合はエラー
        if($start > $wt_work_end)
        {
            throw new \Exception('開始時刻が終業時刻より後です');
        }

        // 終了時刻が始業時刻より前の場合はエラー
        if($end < $wt_work_start)
        {
            throw new \Exception('終了時刻が始業時刻より前です');
        }

        // 昼休憩が設定されている場合
        if(!empty($ClientWorkType->wt_lunch_break_start)
        && !empty($ClientWorkType->wt_lunch_break_end)
        )
        {
            $wt_lunch_break_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_lunch_break_start);   // DateTime
            $wt_lunch_break_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_lunch_break_end);   // DateTime
            if($wt_lunch_break_end < $wt_lunch_break_start)
            {
                $wt_lunch_break_end->add(new DateInterval('P1D'));
            }
    
            // (1) 開始時刻≦昼休憩開始かつ昼休憩終了≦終了時刻　就業時間から昼休憩時間を引く
            // 昼休憩をまたぐ場合
            if($start <= $wt_lunch_break_start
            && $wt_lunch_break_end <= $end)
            {
                // 昼休み時間を差し引く
                $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $wt_lunch_break_start->diff($wt_lunch_break_end));
            }
            
            // (4)終了時刻≦昼休憩開始または昼休憩終了≦開始時刻　昼休憩は無視する
            // 昼休憩にかからない場合
            elseif($start >= $wt_lunch_break_end
            || $wt_lunch_break_start >= $end)
            {
                // 何もしない
            }

            // (2) 開始時刻≧昼休憩開始かつ昼休憩終了≦終了時刻　昼休憩終了を開始時刻にする
            // 昼休憩途中から勤務する場合
            elseif($start >= $wt_lunch_break_start
            && $wt_lunch_break_end <= $end)
            {
                $start = $wt_lunch_break_end;
                $work_hours = $start->diff($end);
            }
            
            // (3) 開始時刻≦昼休憩開始かつ昼休憩終了≧終了時刻　昼休憩開始を終了時刻にする
            // 昼休憩途中まで勤務する場合
            elseif($start <= $wt_lunch_break_start
            && $wt_lunch_break_end >= $end)
            {
                $end = $wt_lunch_break_start;
                $work_hours = $start->diff($end);
            }
        }

        // 夕休憩が設定されている場合
        if(!empty($ClientWorkType->wt_evening_break_start)
        && !empty($ClientWorkType->wt_evening_break_end)
        )
        {
            $wt_evening_break_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_evening_break_start);
            $wt_evening_break_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_evening_break_end);
            if($wt_evening_break_end < $wt_evening_break_start)
            {
                $wt_evening_break_end->add(new DateInterval('P1D'));
            }

            // (11) 開始時刻≦深夜休憩開始かつ夕休憩終了≧終了時刻　夕休憩開始を終了時刻にする
            // 夕休憩中に終業した場合
            if($start <= $wt_evening_break_start
            && $wt_evening_break_end >= $end)
            {
                $end = $wt_evening_break_start;
                $work_hours = $start->diff($end);
            }
            // (12) 開始時刻≦夕休憩開始かつ夕休憩終了≦終了時刻　就業時間から夕休憩時間を引く
            // 夕休憩後に終業した場合
            if($start <= $wt_evening_break_start
            && $wt_evening_break_end <= $end)
            {
                // 夕休憩時間を差し引く
                $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $wt_evening_break_start->diff($wt_evening_break_end));
            }
        }

        // 就業時間がマイナスになった場合はゼロとする
        return $work_hours->invert ? new DateInterval('PT0S') : $work_hours;
    }
    // 2.1. 日勤 b) 休憩時間が決められている場合
    public function rukuruUtilWorkHoursDayBreakHours($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // 休憩時間を 考慮しない就業時間
        $work_hours = $start->diff($end);   // DateInterval

        // (1) 終了時刻が終業時刻以前なら、就業時間から昼休憩時間を引く
        if($end <= $ClientWorkType->wt_work_end && !empty($ClientWorkType->wt_lunch_break))
        {
            $diLunchBreak = $this->rukuruUtilTimeToDateInterval($ClientWorkType->wt_lunch_break);   // DateInterval
            $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $diLunchBreak);
        }

        // (2) 終了時刻が終業時刻以降なら、就業時間から夕休憩時間を引く
        if($end >= $ClientWorkType->wt_work_end && !empty($ClientWorkType->wt_evening_break))
        {
            $diEveningBreak = $this->rukuruUtilTimeToDateInterval($ClientWorkType->wt_evening_break);   // DateInterval
            $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $diEveningBreak);
        }

        // 就業時間がマイナスになった場合はゼロとする
        return $work_hours->invert ? new DateInterval('PT0S') : $work_hours;
    }
    // 2.1. 日勤
    public function rukuruUtilWorkHoursDay($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // b) 休憩時刻が決められていない場合
        if(empty($ClientWorkType->wt_lunch_break_start)
        && empty($ClientWorkType->wt_lunch_break_end)
        && empty($ClientWorkType->wt_evening_break_start)
        && empty($ClientWorkType->wt_evening_break_end)
        )
        {
            return $this->rukuruUtilWorkHoursDayBreakHours($currentDate, $start, $end, $ClientWorkType);
        }

        // a) 休憩時刻が決められている場合
        return $this->rukuruUtilWorkHoursDayBreakTime($currentDate, $start, $end, $ClientWorkType);
    }
    //
    // 2.2. 夜勤 a) 休憩時刻が決められている場合
    public function rukuruUtilWorkHoursNightBreakTime($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // 休憩時間を考慮しない就業時間
        $work_hours = $start->diff($end);

        // 作業種別の開始終業時刻をDateTimeに変換する
        $wt_work_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_work_start); // DateTime
        $wt_work_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_work_end); // DateTime
        if($wt_work_end < $wt_work_start)
        {
            $wt_work_end->add(new DateInterval('P1D'));
        }

        // 開始時刻が終業時刻より後の場合はエラー
        if($start > $ClientWorkType->wt_work_end)
        {
            throw new \Exception('開始時刻が終業時刻より後です');
        }

        // 終了時刻が始業時刻より後の場合はエラー
        if($end < $ClientWorkType->wt_work_start)
        {
            throw new \Exception('終了時刻が始業時刻より前です');
        }

        // 夜休憩が設定されている場合
        if(!empty($ClientWorkType->wt_night_break_start)
        && !empty($ClientWorkType->wt_night_break_end)
        )
        {
            $wt_night_break_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_night_break_start);   // DateTime
            $wt_night_break_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_night_break_end);   // DateTime
            if($wt_night_break_end < $wt_night_break_start)
            {
                $wt_night_break_end->add(new DateInterval('P1D'));
            }
    
            // (1) 開始時刻≦夜休憩開始かつ夜休憩終了≦終了時刻　就業時間から夜休憩時間を引く
            // 夜休憩をまたぐ場合
            if($start <= $ClientWorkType->wt_night_break_start
            && $ClientWorkType->wt_night_break_end <= $end)
            {
                // 夜休み時間を差し引く
                $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $wt_night_break_start->diff($wt_night_break_end));
            }
            
            // (4)終了時刻≦夜休憩開始または夜休憩終了≦開始時刻　夜休憩は無視する
            // 夜休憩にかからない場合
            elseif($start >= $ClientWorkType->wt_night_break_end
            || $ClientWorkType->wt_night_break_start >= $end)
            {
                // 何もしない
            }

            // (2) 開始時刻≧夜休憩開始かつ夜休憩終了≦終了時刻　夜休憩終了を開始時刻にする
            // 夜休憩途中から勤務する場合
            elseif($start >= $ClientWorkType->wt_night_break_start
            && $ClientWorkType->wt_night_break_end <= $end)
            {
                $start = $ClientWorkType->wt_night_break_end;
                $work_hours = $start->diff($end);
            }
            
            // (3) 開始時刻≦夜休憩開始かつ夜休憩終了≧終了時刻　夜休憩開始を終了時刻にする
            // 夜休憩途中まで勤務する場合
            elseif($start <= $ClientWorkType->wt_night_break_start
            && $ClientWorkType->wt_night_break_end >= $end)
            {
                $end = $ClientWorkType->wt_night_break_start;
                $work_hours = $start->diff($end);
            }
        }

        // 深夜休憩が設定されている場合
        if(!empty($ClientWorkType->wt_midnight_break_start)
        && !empty($ClientWorkType->wt_midnight_break_end)
        )
        {
            $wt_midnight_break_start = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_midnight_break_start);
            $wt_midnight_break_end = $this->rukuruUtilTimeToDateTime($currentDate, $ClientWorkType->wt_midnight_break_end);
            if($wt_midnight_break_end < $wt_midnight_break_start)
            {
                $wt_midnight_break_end->add(new DateInterval('P1D'));
            }

            // (1) 開始時刻≦深夜休憩開始かつ深夜休憩終了≦終了時刻　就業時間から深夜休憩時間を引く
            // 深夜休憩をまたぐ場合
            if($start <= $wt_midnight_break_start
            && $wt_midnight_break_end <= $end)
            {
                // 深夜休み時間を差し引く
                $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $wt_midnight_break_start->diff($wt_midnight_break_end));
            }
            
            // (4) 終了時刻≦深夜休憩開始または深夜休憩終了≦開始時刻　深夜休憩は無視する
            // 深夜休憩にかからない場合
            elseif($start >= $wt_midnight_break_end
            || $wt_midnight_break_start >= $end)
            {
                // 何もしない
            }

            // (2) 開始時刻≧深夜休憩開始かつ深夜休憩終了≦終了時刻　深夜休憩終了を開始時刻にする
            // 深夜休憩途中から勤務する場合
            elseif($start >= $wt_midnight_break_start
            && $wt_midnight_break_end <= $end)
            {
                $start = $wt_midnight_break_end;
                $work_hours = $start->diff($end);
            }
            
            // (3) 開始時刻≦深夜休憩開始かつ深夜休憩終了≧終了時刻　深夜休憩開始を終了時刻にする
            // 深夜休憩途中まで勤務する場合
            elseif($start <= $wt_midnight_break_start
            && $wt_midnight_break_end >= $end)
            {
                $end = $wt_midnight_break_start;
                $work_hours = $start->diff($end);
            }
        }

        // 就業時間がマイナスになった場合はゼロとする
        return $work_hours->invert ? new DateInterval('PT0S') : $work_hours;
    }
    // 2.2. 夜勤 b) 休憩時間が決められている場合
    public function rukuruUtilWorkHoursNightBreakHours($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // 休憩時間を 考慮しない就業時間
        $work_hours = $start->diff($end);   // DateInterval

        // 開始時間(H)
        $start_hour = $start->format('H');
        // 終了時間(H)
        $end_hour = $end->format('H');

        // (1) 夜休憩時間があり、開始時刻が05〜22時なら、就業時間から夜休憩時間を引く
        if(!empty($ClientWorkType->wt_night_break)
        && ($start_hour >= 5 && $start_hour < 22))
        {
            $diNightBreak = $this->rukuruUtilTimeToDateInterval($ClientWorkType->wt_night_break);   // DateInterval
            $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $diNightBreak);
        }

        // (2) 深夜休憩時間があり、終了時刻が22〜05時以降、就業時間から深夜休憩時間を引く
        if(!empty($ClientWorkType->wt_midnight_break)
        && ($end_hour >= 22 || $end_hour < 5))
        {
            $diMidnightBreak = $this->rukuruUtilTimeToDateInterval($ClientWorkType->wt_midnight_break);   // DateInterval
            $work_hours = $this->rukuruUtilDateIntervalSub($work_hours, $diMidnightBreak);
        }
        // 就業時間がマイナスになった場合はゼロとする
        return $work_hours->invert ? new DateInterval('PT0S') : $work_hours;
    }
    // 2.2. 夜勤
    public function rukuruUtilWorkHoursNight($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // b) 休憩時刻が決められていない場合
        if(empty($ClientWorkType->wt_night_break_start)
        && empty($ClientWorkType->wt_night_break_end)
        && empty($ClientWorkType->wt_midnight_break_start)
        && empty($ClientWorkType->wt_midnight_break_end)
        )
        {
            return $this->rukuruUtilWorkHoursNightBreakHours($currentDate, $start, $end, $ClientWorkType);
        }

        // a) 休憩時刻が決められている場合
        return $this->rukuruUtilWorkHoursNightBreakTime($currentDate, $start, $end, $ClientWorkType);
    }
    // 2. 休憩を除く就業時間を計算する
    public function rukuruUtilWorkHours($currentDate, $start, $end, $ClientWorkType) : DateInterval
    {
        // 日勤と夜勤の別
        return ($ClientWorkType->wt_day_night == 2)
         ? $this->rukuruUtilWorkHoursNight($currentDate, $start, $end, $ClientWorkType)
          : $this->rukuruUtilWorkHoursDay($currentDate, $start, $end, $ClientWorkType);
    }

    /**
     * 金額文字列を数値化する
     * @param string $value 入力文字列
     * @param float|null $nullValue
     * @return int|null
     */
    public function rukuruUtilMoneyValue($value, $nullValue = null)
    {
        // 全角文字を半角に変換
        $value = trim(mb_convert_kana($value, 'as'));   // 英数字と記号、空白を半角に変換
        // マイナス値の検知
        $sign = substr($value, 0, 1) === '-' ? -1 : 1;
        // 数字とピリオド以外の文字を削除
        $value = preg_replace('/[^0-9.]/', '', $value);
        
        // 最初のピリオドのみ有効
        $parts = explode('.', $value);
        $value = empty($parts[1]) ? $parts[0] : ($parts[0] . '.' . $parts[1]);
        return $value === '' ? $nullValue : ($value * $sign);
    }

    /**
     * 休日判定
     * @param integer $client_id
     * @param string $date
     * @return tinyint 1: 法定休日 2: 法定外休日 3: 祝日 4: 顧客休日 0: 休日でない
     * @throws Exception
     */
    public function rukuruUtilIsHoliday($client_id, string $date) : bool
    {
        $Client = modelClients::find($client_id);
        if(!$Client)
        {
            return throw new \Exception('顧客が見つかりません[' . $client_id . ']');
        }

        // 顧客の休日判定
        if($type = $Client->typeOfHoliday($date))
        {
            return $type;
        }

        // 休日判定
        if($type = holiday::typeOfHoliday($date))
        {
            return $type;
        }

        return 0;
    }

    /**
     * 従業員の時給・請求単価を取得する
     * @param modelClientWorkTypes $ClientWorkType
     * @param integer $employee_id
     * @param integer $client_id
     * @param integer $clientplace_id
     * @param string $wt_cd
     * @return integer[] [標準時給, 残業時給, 深夜残業時給, 法定休日時給, 法定休日深夜残業時給,
     *                    標準請求, 残業請求, 深夜残業請求, 法定休日請求, 法定休日深夜残業請求]
     * @throws Exception 単価が設定されていない場合
     */
    public function rukuruUtilGetEmployeeHourlyRates($ClientWorkType, $employee_id, $client_id, $clientplace_id, $wt_cd) : array
    {
        // 従業員単価レコードがあるばあい
        $EmployeePay = modelEmployeePays::getPayhour($employee_id, $client_id, $clientplace_id, $wt_cd);
        if($EmployeePay)
        {
            return [
                'wt_pay_std'                => str_replace(',', '', $EmployeePay->wt_pay_std),
                'wt_pay_ovr'                => str_replace(',', '', $EmployeePay->wt_pay_ovr),
                'wt_pay_ovr_midnight'       => str_replace(',', '', $EmployeePay->wt_pay_ovr_midnight),
                'wt_pay_holiday'            => str_replace(',', '', $EmployeePay->wt_pay_holiday),
                'wt_pay_holiday_midnight'   => str_replace(',', '', $EmployeePay->wt_pay_holiday_midnight),
                'wt_bill_std'               => str_replace(',', '', $EmployeePay->wt_bill_std),
                'wt_bill_ovr'               => str_replace(',', '', $EmployeePay->wt_bill_ovr),
                'wt_bill_ovr_midnight'      => str_replace(',', '', $EmployeePay->wt_bill_ovr_midnight),
                'wt_bill_holiday'           => str_replace(',', '', $EmployeePay->wt_bill_holiday),
                'wt_bill_holiday_midnight'  => str_replace(',', '', $EmployeePay->wt_bill_holiday_midnight),
            ];
        }
        // 作業種別単価レコードを探す
        return [
            'wt_pay_std'                => str_replace(',', '', $ClientWorkType->wt_pay_std),
            'wt_pay_ovr'                => str_replace(',', '', $ClientWorkType->wt_pay_ovr),
            'wt_pay_ovr_midnight'       => str_replace(',', '', $ClientWorkType->wt_pay_ovr_midnight),
            'wt_pay_holiday'            => str_replace(',', '', $ClientWorkType->wt_pay_holiday),
            'wt_pay_holiday_midnight'   => str_replace(',', '', $ClientWorkType->wt_pay_holiday_midnight),
            'wt_bill_std'               => str_replace(',', '', $ClientWorkType->wt_bill_std),
            'wt_bill_ovr'               => str_replace(',', '', $ClientWorkType->wt_bill_ovr),
            'wt_bill_ovr_midnight'      => str_replace(',', '', $ClientWorkType->wt_bill_ovr_midnight),
            'wt_bill_holiday'           => str_replace(',', '', $ClientWorkType->wt_bill_holiday),
            'wt_bill_holiday_midnight'  => str_replace(',', '', $ClientWorkType->wt_bill_holiday_midnight),
        ];
    }
}
