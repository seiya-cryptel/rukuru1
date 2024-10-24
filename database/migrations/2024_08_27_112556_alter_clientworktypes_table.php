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
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->unsignedTinyInteger('wt_day_night')->default(1)->comment('日勤夜勤')->after('wt_alpha');
            $table->time('wt_work_start')->nullable()->comment('標準始業時間')->after('wt_day_night');
            $table->time('wt_work_end')->nullable()->comment('標準終業時間')->after('wt_work_start');
            $table->time('wt_lunch_break')->nullable()->comment('昼休憩')->after('wt_work_end');
            $table->time('wt_evening_break')->nullable()->comment('有休憩')->after('wt_lunch_break');
            $table->time('wt_night_break')->nullable()->comment('夜休憩')->after('wt_evening_break');
            $table->time('wt_midnight_break')->nullable()->comment('深夜休憩')->after('wt_night_break');
            $table->decimal('wt_pay_std')->nullable()->comment('標準時給')->after('wt_midnight_break');
            $table->decimal('wt_pay_ovr')->nullable()->comment('残業')->after('wt_pay_std');
            $table->decimal('wt_pay_ovr_midnight')->nullable()->comment('深夜')->after('wt_pay_ovr');
            $table->decimal('wt_pay_holiday')->nullable()->comment('法定休日')->after('wt_pay_ovr_midnight');
            $table->decimal('wt_pay_holiday_midnight')->nullable()->comment('法定休日深夜')->after('wt_pay_holiday');
            $table->decimal('wt_bill_std')->nullable()->comment('標準請求')->after('wt_pay_holiday_midnight');
            $table->decimal('wt_bill_ovr')->nullable()->comment('残業')->after('wt_bill_std');
            $table->decimal('wt_bill_ovr_midnight')->nullable()->comment('深夜')->after('wt_bill_ovr');
            $table->decimal('wt_bill_holiday')->nullable()->comment('法定休日')->after('wt_bill_ovr_midnight');
            $table->decimal('wt_bill_holiday_midnight')->nullable()->comment('法定休日深夜')->after('wt_bill_holiday');
        });

        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->unsignedTinyInteger('wt_day_night')->default(1)->comment('日勤夜勤')->after('wt_alpha');
            $table->time('wt_work_start')->nullable()->comment('標準始業時間')->after('wt_day_night');
            $table->time('wt_work_end')->nullable()->comment('標準終業時間')->after('wt_work_start');
            $table->time('wt_lunch_break')->nullable()->comment('昼休憩')->after('wt_work_end');
            $table->time('wt_evening_break')->nullable()->comment('有休憩')->after('wt_lunch_break');
            $table->time('wt_night_break')->nullable()->comment('夜休憩')->after('wt_evening_break');
            $table->time('wt_midnight_break')->nullable()->comment('深夜休憩')->after('wt_night_break');
            $table->decimal('wt_pay_std')->nullable()->comment('標準時給')->after('wt_midnight_break');
            $table->decimal('wt_pay_ovr')->nullable()->comment('残業')->after('wt_pay_std');
            $table->decimal('wt_pay_ovr_midnight')->nullable()->comment('深夜')->after('wt_pay_ovr');
            $table->decimal('wt_pay_holiday')->nullable()->comment('法定休日')->after('wt_pay_ovr_midnight');
            $table->decimal('wt_pay_holiday_midnight')->nullable()->comment('法定休日深夜')->after('wt_pay_holiday');
            $table->decimal('wt_bill_std')->nullable()->comment('標準請求')->after('wt_pay_holiday_midnight');
            $table->decimal('wt_bill_ovr')->nullable()->comment('残業')->after('wt_bill_std');
            $table->decimal('wt_bill_ovr_midnight')->nullable()->comment('深夜')->after('wt_bill_ovr');
            $table->decimal('wt_bill_holiday')->nullable()->comment('法定休日')->after('wt_bill_ovr_midnight');
            $table->decimal('wt_bill_holiday_midnight')->nullable()->comment('法定休日深夜')->after('wt_bill_holiday');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_delete');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_update AFTER UPDATE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, 
                `wt_day_night`, `wt_work_start`, `wt_work_end`, 
                `wt_lunch_break`, `wt_evening_break`, `wt_night_break`, `wt_midnight_break`,
                `wt_pay_std`, `wt_pay_ovr`, `wt_pay_ovr_midnight`, `wt_pay_holiday`, `wt_pay_holiday_midnight`,
                `wt_bill_std`, `wt_bill_ovr`, `wt_bill_ovr_midnight`, `wt_bill_holiday`, `wt_bill_holiday_midnight`,
                `wt_notes`)
            VALUES 
                ("U", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, 
                OLD.wt_day_night, OLD.wt_work_start, OLD.wt_work_end,
                OLD.wt_lunch_break, OLD.wt_evening_break, OLD.wt_night_break, OLD.wt_midnight_break,
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight,
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight,
                OLD.wt_notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_delete AFTER DELETE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, 
                `wt_day_night`, `wt_work_start`, `wt_work_end`, 
                `wt_lunch_break`, `wt_evening_break`, `wt_night_break`, `wt_midnight_break`,
                `wt_pay_std`, `wt_pay_ovr`, `wt_pay_ovr_midnight`, `wt_pay_holiday`, `wt_pay_holiday_midnight`,
                `wt_bill_std`, `wt_bill_ovr`, `wt_bill_ovr_midnight`, `wt_bill_holiday`, `wt_bill_holiday_midnight`,
                `wt_notes`)
            VALUES 
                ("D", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, 
                OLD.wt_day_night, OLD.wt_work_start, OLD.wt_work_end,
                OLD.wt_lunch_break, OLD.wt_evening_break, OLD.wt_night_break, OLD.wt_midnight_break,
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight,
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight,
                OLD.wt_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->dropColumn('wt_day_night');
            $table->dropColumn('wt_work_start');
            $table->dropColumn('wt_work_end');
            $table->dropColumn('wt_lunch_break');
            $table->dropColumn('wt_evening_break');
            $table->dropColumn('wt_night_break');
            $table->dropColumn('wt_midnight_break');
            $table->dropColumn('wt_pay_std');
            $table->dropColumn('wt_pay_ovr');
            $table->dropColumn('wt_pay_ovr_midnight');
            $table->dropColumn('wt_pay_holiday');
            $table->dropColumn('wt_pay_holiday_midnight');
            $table->dropColumn('wt_bill_std');
            $table->dropColumn('wt_bill_ovr');
            $table->dropColumn('wt_bill_ovr_midnight');
            $table->dropColumn('wt_bill_holiday');
            $table->dropColumn('wt_bill_holiday_midnight');
        });

        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->dropColumn('wt_day_night');
            $table->dropColumn('wt_work_start');
            $table->dropColumn('wt_work_end');
            $table->dropColumn('wt_lunch_break');
            $table->dropColumn('wt_evening_break');
            $table->dropColumn('wt_night_break');
            $table->dropColumn('wt_midnight_break');
            $table->dropColumn('wt_pay_std');
            $table->dropColumn('wt_pay_ovr');
            $table->dropColumn('wt_pay_ovr_midnight');
            $table->dropColumn('wt_pay_holiday');
            $table->dropColumn('wt_pay_holiday_midnight');
            $table->dropColumn('wt_bill_std');
            $table->dropColumn('wt_bill_ovr');
            $table->dropColumn('wt_bill_ovr_midnight');
            $table->dropColumn('wt_bill_holiday');
            $table->dropColumn('wt_bill_holiday_midnight');
        });
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_delete');
    }
};
