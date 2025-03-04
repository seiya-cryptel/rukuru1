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
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->string('wt_name')->comment('作業名')->after('wt_cd');
            $table->time('wrk_break')->nullable()->comment('休憩時間')->after('wrk_work_end');
            $table->unsignedTinyInteger('summary_index')->default(1)->comment('集計インデクス')->after('wrk_work_hours');
            $table->string('summary_name')->comment('集計項目名')->after('summary_index');
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->string('wt_name')->comment('作業名')->after('wt_cd');
            $table->time('wrk_break')->nullable()->comment('休憩時間')->after('wrk_work_end');
            $table->unsignedTinyInteger('summary_index')->default(1)->comment('集計インデクス')->after('wrk_work_hours');
            $table->string('summary_name')->comment('集計項目名')->after('summary_index');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_delete');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_update AFTER UPDATE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `employeework_id`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `client_id`, `clientplace_id`, 
                `holiday_type`, `work_type`, `wt_cd`, `wt_name`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_break`, `wrk_work_hours`, `summary_index`, `summary_name`,
                `payhour`, `wrk_pay`, `billhour`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.holiday_type, OLD.work_type, OLD.wt_cd, OLD.wt_name,
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_break, OLD.wrk_work_hours, OLD.summary_index, OLD.summary_name,
                OLD.payhour, OLD.wrk_pay, OLD.billhour, OLD.wrk_bill,
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_delete AFTER DELETE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `employeework_id`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `client_id`, `clientplace_id`, 
                `holiday_type`, `work_type`, `wt_cd`, `wt_name`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_break`, `wrk_work_hours`, `summary_index`, `summary_name`,
                `payhour`, `wrk_pay`, `billhour`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.holiday_type, OLD.work_type, OLD.wt_cd, OLD.wt_name,
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_break, OLD.wrk_work_hours, OLD.summary_index, OLD.summary_name,
                OLD.payhour, OLD.wrk_pay, OLD.billhour, OLD.wrk_bill,
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_delete');

        Schema::table('employeeworks', function (Blueprint $table) {
            $table->dropColumn('wt_name');
            $table->dropColumn('wrk_break');
            $table->dropColumn('summary_index');
            $table->dropColumn('summary_name');
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->dropColumn('wt_name');
            $table->dropColumn('wrk_break');
            $table->dropColumn('summary_index');
            $table->dropColumn('summary_name');
        });
    }
};