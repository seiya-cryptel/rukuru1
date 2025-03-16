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
        Schema::table('salarys', function (Blueprint $table) {
            $table->decimal('paid_leave_pay', 12, 4)->default(0)->comment('有給日当')->after('work_month');
            $table->unsignedTinyInteger('working_regular_days')->default(0)->comment('平日出勤日数')->after('paid_leave_pay');
            $table->unsignedTinyInteger('non_statutory_days')->default(0)->comment('法定外休日出勤日数')->after('working_regular_days');
            $table->unsignedTinyInteger('statutory_days')->default(0)->comment('法定休日出勤日数')->after('non_statutory_days');
            $table->unsignedTinyInteger('paid_leave_days')->default(0)->comment('有給取得日数')->after('statutory_days');
            $table->unsignedTinyInteger('working_days')->default(0)->comment('出勤日数')->after('paid_leave_days');
        });
        Schema::table('xlog_salarys', function (Blueprint $table) {
            $table->decimal('paid_leave_pay', 12, 4)->default(0)->comment('有給日当')->after('work_month');
            $table->unsignedTinyInteger('working_regular_days')->default(0)->comment('平日出勤日数')->after('paid_leave_pay');
            $table->unsignedTinyInteger('non_statutory_days')->default(0)->comment('法定外休日出勤日数')->after('working_regular_days');
            $table->unsignedTinyInteger('statutory_days')->default(0)->comment('法定休日出勤日数')->after('non_statutory_days');
            $table->unsignedTinyInteger('paid_leave_days')->default(0)->comment('有給取得日数')->after('statutory_days');
            $table->unsignedTinyInteger('working_days')->default(0)->comment('出勤日数')->after('paid_leave_days');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_delete');

        DB::unprepared('CREATE TRIGGER trg_salarys_update AFTER UPDATE ON `salarys` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_salarys`
                    (`opr`, `salary_id`, `employee_id`, `work_year`, `work_month`, `paid_leave_pay`,
                    `working_regular_days`, `non_statutory_days`, `statutory_days`, `paid_leave_days`, `working_days`,
                    `work_amount`, `allow_amount`, `deduct_amount`, `transport`, `pay_amount`, `notes`)
                VALUES 
                    ("U", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, OLD.paid_leave_pay,
                    OLD.working_regular_days, OLD.non_statutory_days, OLD.statutory_days, OLD.paid_leave_days, OLD.working_days,
                    OLD.work_amount, OLD.allow_amount, OLD.deduct_amount, OLD.transport, OLD.pay_amount, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_salarys_delete AFTER DELETE ON `salarys` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_salarys`
                    (`opr`, `salary_id`, `employee_id`, `work_year`, `work_month`, `paid_leave_pay`,
                    `working_regular_days`, `non_statutory_days`, `statutory_days`, `paid_leave_days`, `working_days`,
                    `work_amount`, `allow_amount`, `deduct_amount`, `transport`, `pay_amount`, `notes`)
                VALUES 
                    ("D", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, OLD.paid_leave_pay,
                    OLD.working_regular_days, OLD.non_statutory_days, OLD.statutory_days, OLD.paid_leave_days, OLD.working_days,
                    OLD.work_amount, OLD.allow_amount, OLD.deduct_amount, OLD.transport, OLD.pay_amount, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_delete');

        Schema::table('salarys', function (Blueprint $table) {
            $table->dropColumn('paid_leave_pay');
            $table->dropColumn('working_regular_days');
            $table->dropColumn('non_statutory_days');
            $table->dropColumn('statutory_days');
            $table->dropColumn('paid_leave_days');
            $table->dropColumn('working_days');
        });
        Schema::table('xlog_salarys', function (Blueprint $table) {
            $table->dropColumn('paid_leave_pay');
            $table->dropColumn('working_regular_days');
            $table->dropColumn('non_statutory_days');
            $table->dropColumn('statutory_days');
            $table->dropColumn('paid_leave_days');
            $table->dropColumn('working_days');
        });
    }
};
