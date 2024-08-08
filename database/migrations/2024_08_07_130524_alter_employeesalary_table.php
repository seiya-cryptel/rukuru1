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
        Schema::table('employeesalary', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl')->change();
            $table->tinyInteger('wrk_seq')->comment('勤怠通し番号')->change();
            $table->tinyInteger('leave')->default(0)->comment('休暇フラグ')->after('wrk_seq');
            $table->unsignedBigInteger('client_id')->comment('顧客ID')->after('leave');
            $table->unsignedBigInteger('clientplace_id')->comment('事業所ID')->after('client_id');
            $table->string('wt_cd', 8)->comment('作業種類コード')->after('clientplace_id');
            $table->time('wrk_work_hours')->comment('勤務時間数')->change();

            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('wrk_seq', 'wrk_ttl_seq');
        });

        Schema::table('xlog_employeesalary', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl_slry')->change();
            $table->unsignedBigInteger('id_empl')->change();
            $table->tinyInteger('leave')->after('wrk_seq');
            $table->unsignedBigInteger('client_id')->after('leave');
            $table->unsignedBigInteger('clientplace_id')->after('client_id');
            $table->string('wt_cd', 8)->after('clientplace_id');
            $table->time('wrk_work_hours')->change();

            $table->renameColumn('id_empl_slry', 'employeesalary_id');
            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('wrk_seq', 'wrk_ttl_seq');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_delete');

        DB::unprepared('CREATE TRIGGER trg_employeesalary_update AFTER UPDATE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `employeesalary_id`, `employee_id`, `wrk_date`, `wrk_ttl_seq`, `leave`, `client_id`, `clientplace_id`, `wt_cd`,
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_ttl_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, OLD.wt_cd,
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeesalary_delete AFTER DELETE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `employeesalary_id`, `employee_id`, `wrk_date`, `wrk_ttl_seq`, `leave`, `client_id`, `clientplace_id`, `wt_cd`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_ttl_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, OLD.wt_cd,
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeesalary', function (Blueprint $table) {
            $table->dropColumn('leave');
            $table->dropColumn('client_id');
            $table->dropColumn('clientplace_id');
            $table->dropColumn('wt_cd');

            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('wrk_ttl_seq', 'wrk_seq');
        });

        Schema::table('xlog_employeesalary', function (Blueprint $table) {
            $table->dropColumn('leave');
            $table->dropColumn('client_id');
            $table->dropColumn('clientplace_id');
            $table->dropColumn('wt_cd');

            $table->renameColumn('employeesalary_id', 'id_empl_slry');
            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('wrk_ttl_seq', 'wrk_seq');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_delete');
    }
};
