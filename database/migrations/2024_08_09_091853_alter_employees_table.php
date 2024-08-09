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
        Schema::table('xlog_employees', function (Blueprint $table) {
            $table->string('empl_sex', 4)->nullable()->comment('性別')->after('empl_alpha_first');
            $table->dateTime('empl_hire_date')->nullable()->comment('入社日')->after('empl_mobile');
            $table->dateTime('empl_resign_date')->nullable()->comment('退社日')->after('empl_hire_date');

            $table->renameColumn('empl_name_midle', 'empl_name_middle');
            $table->renameColumn('empl_kana_midle', 'empl_kana_middle');
            $table->renameColumn('empl_alpha_midle', 'empl_alpha_middle');
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
                    `empl_email`, `empl_mobile`, `empl_notes`)
            VALUES 
                ("U", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_middle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_middle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_middle, OLD.empl_alpha_first,
                    OLD.empl_sex, OLD.empl_hire_date, OLD.empl_resign_date,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employees_delete AFTER DELETE ON `employees` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employees`
                (`opr`, `id_empl`, `empl_cd`, 
                    `empl_name_last`, `empl_name_middle`, `empl_name_first`,
                    `empl_kana_last`, `empl_kana_middle`, `empl_kana_first`,
                    `empl_alpha_last`, `empl_alpha_middle`, `empl_alpha_first`,
                    `empl_sex`, `empl_hire_date`, `empl_resign_date`,
                    `empl_email`, `empl_mobile`, `empl_notes`)
            VALUES 
                ("D", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_middle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_middle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_middle, OLD.empl_alpha_first,
                    OLD.empl_sex, OLD.empl_hire_date, OLD.empl_resign_date,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
