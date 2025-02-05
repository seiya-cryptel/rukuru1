<?php

use App\Traits\RukuruUtilities;

class TestRukuruUtilities
{
    use RukuruUtilities;
}

/**
 * empty -> null 変換
 * '' は null に変換される
 * それ以外はそのまま返す
 */
test('test rukuruUtilEmptyToNull', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(null, $testRukuruUtilities->rukuruUtilEmptyToNull(''));
    self::assertEquals('test', $testRukuruUtilities->rukuruUtilEmptyToNull('test'));
});

/**
 * DateInterval の差分
 * 2H - 1H = 1H
 * 1H - 2H = -1H
 * 1D1H - 2H = 23H  (1日1時間 - 2時間 = 23時間)
 */
test('test rukuruUtilDateIntervalSub', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    // 正
    self::assertEquals(
        new DateInterval('PT1H'), 
        $testRukuruUtilities->rukuruUtilDateIntervalSub(new DateInterval('PT2H'), new DateInterval('PT1H'))
    ); 
    // 負
    $diNegative1H = new DateInterval('PT1H');
    $diNegative1H->invert = 1;
    self::assertEquals(
        $diNegative1H, 
        $testRukuruUtilities->rukuruUtilDateIntervalSub(new DateInterval('PT1H'), new DateInterval('PT2H'))
    ); 
    // 日付の時間差分
    self::assertEquals(
        new DateInterval('PT23H'), 
        $testRukuruUtilities->rukuruUtilDateIntervalSub(new DateInterval('P1DT1H'), new DateInterval('PT2H'))
    ); 
});

/**
 * 時刻文字列の正規化
 * 正常系
 * 345 -> 03:45     数字
 * 1456 -> 14:56
 * 3:45 -> 03:45    数字 記号 数字
 * 11.30 -> 11:30
 * 15,30 -> 15:30
 * 
 * 異常系
 * 8
 * 23
 * 12345
 * 123:45
 * 12:345
 * 3:4:5
 * 12-30
 */
test('test rukuruUtilTimeNormalize Normal', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals('03:45', $testRukuruUtilities->rukuruUtilTimeNormalize('345'));
    self::assertEquals('14:56', $testRukuruUtilities->rukuruUtilTimeNormalize('1456'));
    self::assertEquals('03:45', $testRukuruUtilities->rukuruUtilTimeNormalize('3:45'));
    self::assertEquals('11:30', $testRukuruUtilities->rukuruUtilTimeNormalize('11.30'));
    self::assertEquals('15:30', $testRukuruUtilities->rukuruUtilTimeNormalize('15,30'));
});
test('test rukuruUtilTimeNormalize Exception 8', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('8');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 23', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('23');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 12345', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('12345');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 123:45', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('123:45');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 12:345', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('12:345');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 3.4.5', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('3.4.5');
})->throws(Exception::class);
test('test rukuruUtilTimeNormalize Exception 12-30', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilTimeNormalize('12-30');
})->throws(Exception::class);

/**
 * 文字列の時刻をDateTimeオブジェクトに変換する
 * @param DateTime $date 日付
 * @param string $time 時刻
 * @return DateTime
 * 5 時より前の場合は翌日として扱う
 * 時刻は正規化されているものとする
 */
test('test rukuruUtilDateTimeFromTime', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $date = new DateTime('2021-01-01');
    self::assertEquals(
        new DateTime('2021-01-02 03:45'), 
        $testRukuruUtilities->rukuruUtilTimeToDateTime($date, '03:45')
    );
    self::assertEquals(
        new DateTime('2021-01-01 14:56'), 
        $testRukuruUtilities->rukuruUtilTimeToDateTime($date, '14:56')
    );
});

/**
 * hh:mm, hh:mm:ss 文字列を DateInterval に変換する
 * @param string $time
 * @return DateInterval
 */
test('test rukuruUtilTimeToDateInterval', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(
        new DateInterval('PT3H45M'), 
        $testRukuruUtilities->rukuruUtilTimeToDateInterval('03:45')
    );
    self::assertEquals(
        new DateInterval('PT14H56M'), 
        $testRukuruUtilities->rukuruUtilTimeToDateInterval('14:56')
    );
});

/**
 * DateInterval を hh:mm 形式の文字列に変換する
 * @param DateInterval $interval
 * @return string
 */
test('test rukuruUtilDateIntervalFormat', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(
        '03:45', 
        $testRukuruUtilities->rukuruUtilDateIntervalFormat(new DateInterval('PT3H45M'))
    );
    self::assertEquals(
        '14:56', 
        $testRukuruUtilities->rukuruUtilDateIntervalFormat(new DateInterval('PT14H56M'))
    );
});

/**
 * 開始時刻をまるめる
 * @param DateTime $time
 * @param integer $minutes
 * @return DateTime
 */
test('test rukuruUtilTimeRoundUp', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(
        new DateTime('2021-01-01 08:45'), 
        $testRukuruUtilities->rukuruUtilTimeRoundUp(new DateTime('2021-01-01 08:40'), 15)
    );
});

/**
 * 終了時刻をまるめる
 * @param DateTime $time
 * @param integer $minutes
 * @return DateTime
 */
test('test rukuruUtilTimeRoundDown', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(
        new DateTime('2021-01-01 17:30'), 
        $testRukuruUtilities->rukuruUtilTimeRoundDown(new DateTime('2021-01-01 17:59'), 30)
    );
});

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
/**
 * 通常の日勤
 * 08:00 - 17:00
 */
test('test rukuruUtilWorkHours daily', function () {
    $testRukuruUtilities = new TestRukuruUtilities();

    $mockClientWorkType = Mockery::mock('clientworktypes');
    $mockClientWorkType->wt_day_night = 1;
    $mockClientWorkType->wt_work_start = '08:00';   // 開始時間
    $mockClientWorkType->wt_work_end = '17:00';    // 終了時間
    $mockClientWorkType->wt_lunch_break_start = '12:00';  // 休憩開始時間
    $mockClientWorkType->wt_lunch_break_end = '13:00';    // 休憩終了時間

    $date = new DateTime('2021-01-01');
    $start = new DateTime('2021-01-01 07:45');
    $end = new DateTime('2021-01-01 17:05');
    self::assertEquals(
        // new DateInterval('PT7H45M'), 
        new DateInterval('PT8H20M'), 
        $testRukuruUtilities->rukuruUtilWorkHours($date, $start, $end, $mockClientWorkType)
    );
});

/**
 * 金額文字列を数値化する
 * @param string $value 入力文字列
 * @param float|null $nullValue
 * @return int|null
 */
test('test rukuruUtilMoneyValue', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    self::assertEquals(1234.5, $testRukuruUtilities->rukuruUtilMoneyValue('1234.5'));
    self::assertEquals(12345, $testRukuruUtilities->rukuruUtilMoneyValue('12,345'));
    self::assertEquals(12345, $testRukuruUtilities->rukuruUtilMoneyValue('12345'));
    self::assertEquals(12.3, $testRukuruUtilities->rukuruUtilMoneyValue('12.3.45'));
    self::assertEquals(0, $testRukuruUtilities->rukuruUtilMoneyValue('', 0));
});

/**
 * 休日判定
 * @param integer $client_id
 * @param string $date
 * @return tinyint 1: 法定休日 2: 法定外休日 3: 祝日 4: 顧客休日 0: 休日でない
 * @throws Exception
 */
/*
test('test rukuruUtilIsHoliday', function () {
    $testRukuruUtilities = new TestRukuruUtilities();
    $testRukuruUtilities->rukuruUtilIsHoliday(3, '2021-01-01');
})->throws(Exception::class);
*/

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
