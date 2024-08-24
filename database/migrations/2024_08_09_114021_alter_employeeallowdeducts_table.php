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
        Schema::table('employeeallowdeducts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl')->change();

            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('wrk_year', 'work_year');
            $table->renameColumn('wrk_month', 'work_month');
        });

        Schema::table('xlog_employeeallowdeducts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl')->change();

            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('wrk_year', 'work_year');
            $table->renameColumn('wrk_month', 'work_month');
        });
 
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_delete');

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_update AFTER UPDATE ON `employeeallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeallowdeducts`
                (`opr`, `id_empl_ad`, `employee_id`, `work_year`, `work_month`, `mad_cd`, `mad_deduct`, `amount`, `notes`)
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, OLD.mad_cd, OLD.mad_deduct, OLD.amount, OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_delete AFTER DELETE ON `employeeallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeallowdeducts`
                (`opr`, `id_empl_ad`, `employee_id`, `work_year`, `work_month`, `mad_cd`, `mad_deduct`, `amount`, `notes`)
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, OLD.mad_cd, OLD.mad_deduct, OLD.amount, OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeeallowdeducts', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('work_year', 'wrk_year');
            $table->renameColumn('work_month', 'wrk_month');
        });

        Schema::table('xlog_employeeallowdeducts', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('work_year', 'wrk_year');
            $table->renameColumn('work_month', 'wrk_month');
        });
    }
};
