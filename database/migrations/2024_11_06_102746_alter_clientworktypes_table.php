<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 作業種別テーブルに休憩時間開始、終了時刻を追加
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->time('wt_lunch_break_start')->nullable()->comment('昼休憩開始時刻')->after('wt_work_end');
            $table->time('wt_lunch_break_end')->nullable()->comment('昼休憩終了時刻')->after('wt_lunch_break_start');

            $table->time('wt_evening_break_start')->nullable()->comment('夕休憩開始時刻')->after('wt_lunch_break');
            $table->time('wt_evening_break_end')->nullable()->comment('夕休憩終了時刻')->after('wt_evening_break_start');

            $table->time('wt_night_break_start')->nullable()->comment('夜休憩開始時刻')->after('wt_evening_break');
            $table->time('wt_night_break_end')->nullable()->comment('夜休憩終了時刻')->after('wt_night_break_start');

            $table->time('wt_midnight_break_start')->nullable()->comment('深夜休憩開始時刻')->after('wt_night_break');
            $table->time('wt_midnight_break_end')->nullable()->comment('深夜休憩終了時刻')->after('wt_midnight_break_start');
        });

        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->time('wt_lunch_break_start')->nullable()->comment('昼休憩開始時刻')->after('wt_work_end');
            $table->time('wt_lunch_break_end')->nullable()->comment('昼休憩終了時刻')->after('wt_lunch_break_start');

            $table->time('wt_evening_break_start')->nullable()->comment('夕休憩開始時刻')->after('wt_lunch_break');
            $table->time('wt_evening_break_end')->nullable()->comment('夕休憩終了時刻')->after('wt_evening_break_start');

            $table->time('wt_night_break_start')->nullable()->comment('夜休憩開始時刻')->after('wt_evening_break');
            $table->time('wt_night_break_end')->nullable()->comment('夜休憩終了時刻')->after('wt_night_break_start');

            $table->time('wt_midnight_break_start')->nullable()->comment('深夜休憩開始時刻')->after('wt_night_break');
            $table->time('wt_midnight_break_end')->nullable()->comment('深夜休憩終了時刻')->after('wt_midnight_break_start');
        });


        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_delete');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_update AFTER UPDATE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, 
                `wt_day_night`, `wt_work_start`, `wt_work_end`, 
                `wt_lunch_break_start`, `wt_lunch_break_end`, `wt_lunch_break`, 
                `wt_evening_break_start`, `wt_evening_break_end`, `wt_evening_break`, 
                `wt_night_break_start`, `wt_night_break_end`, `wt_night_break`, 
                `wt_midnight_break_start`, `wt_midnight_break_end`, `wt_midnight_break`, 
                `wt_pay_std`, `wt_pay_ovr`, `wt_pay_ovr_midnight`, `wt_pay_holiday`, `wt_pay_holiday_midnight`, 
                `wt_bill_std`, `wt_bill_ovr`, `wt_bill_ovr_midnight`, `wt_bill_holiday`, `wt_bill_holiday_midnight`
                )
            VALUES
                (\'U\', OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, 
                OLD.wt_day_night, OLD.wt_work_start, OLD.wt_work_end, 
                OLD.wt_lunch_break_start, OLD.wt_lunch_break_end, OLD.wt_lunch_break, 
                OLD.wt_evening_break_start, OLD.wt_evening_break_end, OLD.wt_evening_break, 
                OLD.wt_night_break_start, OLD.wt_night_break_end, OLD.wt_night_break, 
                OLD.wt_midnight_break_start, OLD.wt_midnight_break_end, OLD.wt_midnight_break, 
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight, 
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight
                );
        END');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_delete AFTER DELETE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, 
                `wt_day_night`, `wt_work_start`, `wt_work_end`, 
                `wt_lunch_break_start`, `wt_lunch_break_end`, `wt_lunch_break`, 
                `wt_evening_break_start`, `wt_evening_break_end`, `wt_evening_break`, 
                `wt_night_break_start`, `wt_night_break_end`, `wt_night_break`, 
                `wt_midnight_break_start`, `wt_midnight_break_end`, `wt_midnight_break`, 
                `wt_pay_std`, `wt_pay_ovr`, `wt_pay_ovr_midnight`, `wt_pay_holiday`, `wt_pay_holiday_midnight`, 
                `wt_bill_std`, `wt_bill_ovr`, `wt_bill_ovr_midnight`, `wt_bill_holiday`, `wt_bill_holiday_midnight`
                )
            VALUES
                (\'D\', OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, 
                OLD.wt_day_night, OLD.wt_work_start, OLD.wt_work_end, 
                OLD.wt_lunch_break_start, OLD.wt_lunch_break_end, OLD.wt_lunch_break, 
                OLD.wt_evening_break_start, OLD.wt_evening_break_end, OLD.wt_evening_break, 
                OLD.wt_night_break_start, OLD.wt_night_break_end, OLD.wt_night_break, 
                OLD.wt_midnight_break_start, OLD.wt_midnight_break_end, OLD.wt_midnight_break, 
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight, 
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight
                );
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 追加した列を削除
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->dropColumn('wt_lunch_break_start');
            $table->dropColumn('wt_lunch_break_end');
            $table->dropColumn('wt_evening_break_start');
            $table->dropColumn('wt_evening_break_end');
            $table->dropColumn('wt_night_break_start');
            $table->dropColumn('wt_night_break_end');
            $table->dropColumn('wt_midnight_break_start');
            $table->dropColumn('wt_midnight_break_end');
        });

        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->dropColumn('wt_lunch_break_start');
            $table->dropColumn('wt_lunch_break_end');
            $table->dropColumn('wt_evening_break_start');
            $table->dropColumn('wt_evening_break_end');
            $table->dropColumn('wt_night_break_start');
            $table->dropColumn('wt_night_break_end');
            $table->dropColumn('wt_midnight_break_start');
            $table->dropColumn('wt_midnight_break_end');
        });
    }
};