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
            $table->decimal('payhour', 12, 4)->default(0)->comment('時給')->after('wrk_work_hours');
            $table->decimal('wrk_pay', 12, 4)->default(0)->comment('金額')->after('payhour');
            $table->decimal('billhour', 12, 4)->default(0)->comment('請求単価')->after('wrk_pay');
            $table->decimal('wrk_bill', 12, 4)->default(0)->comment('請求金額')->after('billhour');
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->decimal('payhour', 12, 4)->default(0)->comment('時給')->after('wrk_work_hours');
            $table->decimal('wrk_pay', 12, 4)->default(0)->comment('金額')->after('payhour');
            $table->decimal('billhour', 12, 4)->default(0)->comment('請求単価')->after('wrk_pay');
            $table->decimal('wrk_bill', 12, 4)->default(0)->comment('請求金額')->after('billhour');
        });
 
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_delete');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_update AFTER UPDATE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `employeework_id`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `client_id`, `clientplace_id`, 
                `holiday_type`, `work_type`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, 
                `payhour`, `wrk_pay`, `billhour`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.holiday_type, OLD.work_type, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, 
                OLD.payhour, OLD.wrk_pay, OLD.billhour, OLD.wrk_bill,
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_delete AFTER DELETE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `employeework_id`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `client_id`, `clientplace_id`, 
                `holiday_type`, `work_type`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, 
                `payhour`, `wrk_pay`, `billhour`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.holiday_type, OLD.work_type, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, 
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
            $table->dropColumn('payhour');
            $table->dropColumn('wrk_pay');
            $table->dropColumn('billhour');
            $table->dropColumn('wrk_bill');
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->dropColumn('payhour');
            $table->dropColumn('wrk_pay');
            $table->dropColumn('billhour');
            $table->dropColumn('wrk_bill');
        });
    }
};
