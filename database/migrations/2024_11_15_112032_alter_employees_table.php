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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('empl_main_client_name', 255)->nullable()->comment('主な顧客')->after('empl_resign_date');
        });
        Schema::table('xlog_employees', function (Blueprint $table) {
            $table->string('empl_main_client_name', 255)->nullable()->comment('主な顧客')->after('empl_resign_date');
        });
 
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_delete');

        DB::unprepared('CREATE TRIGGER trg_employees_update AFTER UPDATE ON `employees` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employees`
                (`opr`, `id_empl`, `empl_cd`, 
                    `empl_name_last`, `empl_name_middle`, `empl_name_first`,
                    `empl_kana_last`, `empl_kana_middle`, `empl_kana_first`,
                    `empl_alpha_last`, `empl_alpha_middle`, `empl_alpha_first`,
                    `empl_sex`, `empl_hire_date`, `empl_resign_date`,
                    `empl_email`, `empl_mobile`, `empl_main_client_name`, `empl_notes`)
            VALUES 
                ("U", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_middle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_middle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_middle, OLD.empl_alpha_first,
                    OLD.empl_sex, OLD.empl_hire_date, OLD.empl_resign_date,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_main_client_name, OLD.empl_notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employees_delete AFTER DELETE ON `employees` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employees`
                (`opr`, `id_empl`, `empl_cd`, 
                    `empl_name_last`, `empl_name_middle`, `empl_name_first`,
                    `empl_kana_last`, `empl_kana_middle`, `empl_kana_first`,
                    `empl_alpha_last`, `empl_alpha_middle`, `empl_alpha_first`,
                    `empl_sex`, `empl_hire_date`, `empl_resign_date`,
                    `empl_email`, `empl_mobile`, `empl_main_client_name`, `empl_notes`)
            VALUES 
                ("D", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_middle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_middle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_middle, OLD.empl_alpha_first,
                    OLD.empl_sex, OLD.empl_hire_date, OLD.empl_resign_date,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_main_client_name, OLD.empl_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('empl_main_client_name');
        });
        Schema::table('xlog_employees', function (Blueprint $table) {
            $table->dropColumn('empl_main_client_name');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_delete');
    }
};
