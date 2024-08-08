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
            $table->time('wrk_work_hours')->nullable()->comment('勤務時間')->change();

            $table->renameColumn('wrk_work_hours', 'wrk_work_time');
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->time('wrk_work_hours')->nullable()->comment('勤務時間')->change();

            $table->renameColumn('wrk_work_hours', 'wrk_work_time');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeworks_delete');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_update AFTER UPDATE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `id_empl_wrk`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `clientplace_id`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_time`, 
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.clientplace_id, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_time, 
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_delete AFTER DELETE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `id_empl_wrk`, `employee_id`, `wrk_date`, `wrk_seq`, `leave`, `clientplace_id`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_time`, 
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.clientplace_id, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_time, 
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->decimal('wrk_work_time', 12, 4)->nullable()->change();
            
            $table->renameColumn('wrk_work_time', 'wrk_work_hours');
        });
    }
};
