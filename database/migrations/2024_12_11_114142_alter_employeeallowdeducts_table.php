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
            $table->unsignedBigInteger('masterallowdeduct_id')->comment('手当控除ID')->after('work_month');
            $table->string('mad_name', 256)->comment('手当控除名')->after('mad_deduct');
        });

        Schema::table('xlog_employeeallowdeducts', function (Blueprint $table) {
            $table->unsignedBigInteger('masterallowdeduct_id')->comment('手当控除ID')->after('work_month');
            $table->string('mad_name', 256)->comment('手当控除名')->after('mad_deduct');
        });
 
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_delete');

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_update AFTER UPDATE ON `employeeallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeallowdeducts`
                (`opr`, `id_empl_ad`, `employee_id`, `work_year`, `work_month`, 
                `masterallowdeduct_id`, `mad_cd`, `mad_deduct`, `mad_name`, `amount`, `notes`)
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, 
                OLD.masterallowdeduct_id, OLD.mad_cd, OLD.mad_deduct, OLD.mad_name, OLD.amount, OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_delete AFTER DELETE ON `employeeallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeallowdeducts`
                (`opr`, `id_empl_ad`, `employee_id`, `work_year`, `work_month`, 
                `masterallowdeduct_id`, `mad_cd`, `mad_deduct`, `mad_name`, `amount`, `notes`)
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, 
                OLD.masterallowdeduct_id, OLD.mad_cd, OLD.mad_deduct, OLD.mad_name, OLD.amount, OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeeallowdeducts', function (Blueprint $table) {
            $table->dropColumn('masterallowdeduct_id');
            $table->dropColumn('mad_name');
        });

        Schema::table('xlog_employeeallowdeducts', function (Blueprint $table) {
            $table->dropColumn('masterallowdeduct_id');
            $table->dropColumn('mad_name');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeeallowdeducts_delete');
    }
};
