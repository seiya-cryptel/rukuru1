<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

use App\Models\clients as modelClients;
use App\Models\holiday as modelHoliday;

/**
 * Trait rukuruUtilites
 * @package App\Traits
 * ツール関数をまとめたTrait
 */
trait rukuruUtilites
{
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
     * 時刻をまるめる
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
    }

    /**
     * 時刻をまるめる
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
    }

    /**
     * 金額文字列を数値化する
     * @param string $value 入力文字列
     * @return int|null
     */
    protected function rukuruUtilMoneyValue($value)
    {
        // 全角文字を半角に変換
        $value = trim(mb_convert_kana($value, 'as'));   // 英数字と記号、空白を半角に変換
        // 数字とピリオド以外の文字を削除
        $value = preg_replace('/[^0-9.]/', '', $value);
        return $value === '' ? null : $value;
    }

    /**
     * 休日判定
     * @param integer $client_id
     * @param string $date
     * @return tinyint 1: 法定休日 2: 法定外休日 3: 祝日 4: 顧客休日 0: 休日でない
     */
    protected function rukuruUtilIsHoliday($client_id, string $date) : bool
    {
        // $dayOfWeek = date('w', strtotime($date));

        $Client = modelClients::find($client_id);

        // 顧客の休日判定
        if($type = $Client->typeOfHoliday($date))
        {
            return $type;
        }

        // 休日判定
        if($type = modelHoliday::typeOfHoliday($date))
        {
            return $type;
        }

        return 0;
    }
}
