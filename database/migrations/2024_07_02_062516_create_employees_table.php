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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('empl_cd', 8)->unique()->comment('従業員コード');
            $table->string('empl_name_last')->comment('従業員姓');
            $table->string('empl_name_midle')->nullable()->comment('従業員ミドルネーム');
            $table->string('empl_name_first')->comment('従業員名');
            $table->string('empl_kana_last')->comment('従業員姓カナ');
            $table->string('empl_kana_midle')->nullable()->comment('従業員ミドルネーム カナ');
            $table->string('empl_kana_first')->comment('従業員名カナ');
            $table->string('empl_alpha_last')->comment('従業員姓英字');
            $table->string('empl_alpha_midle')->nullable()->comment('従業員ミドルネーム英字');
            $table->string('empl_alpha_first')->comment('従業員名英字');
            $table->string('empl_email')->nullable()->comment('従業員メール');
            $table->string('empl_mobile')->nullable()->comment('従業員携帯');
            $table->string('empl_notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_employees', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_empl');
            $table->string('empl_cd', 8);
            $table->string('empl_name_last');
            $table->string('empl_name_midle')->nullable();
            $table->string('empl_name_first');
            $table->string('empl_kana_last');
            $table->string('empl_kana_midle')->nullable();
            $table->string('empl_kana_first');
            $table->string('empl_alpha_last');
            $table->string('empl_alpha_midle')->nullable();
            $table->string('empl_alpha_first');
            $table->string('empl_email')->nullable();
            $table->string('empl_mobile')->nullable();
            $table->string('empl_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_employees_update AFTER UPDATE ON `employees` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employees`
                (`opr`, `id_empl`, `empl_cd`, 
                    `empl_name_last`, `empl_name_midle`, `empl_name_first`,
                    `empl_kana_last`, `empl_kana_midle`, `empl_kana_first`,
                    `empl_alpha_last`, `empl_alpha_midle`, `empl_alpha_first`,
                    `empl_email`, `empl_mobile`, `empl_notes`)
            VALUES 
                ("U", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_midle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_midle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_midle, OLD.empl_alpha_first,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employees_delete AFTER DELETE ON `employees` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employees`
                (`opr`, `id_empl`, `empl_cd`, 
                    `empl_name_last`, `empl_name_midle`, `empl_name_first`,
                    `empl_kana_last`, `empl_kana_midle`, `empl_kana_first`,
                    `empl_alpha_last`, `empl_alpha_midle`, `empl_alpha_first`,
                    `empl_email`, `empl_mobile`, `empl_notes`)
            VALUES 
                ("D", OLD.id, OLD.empl_cd, 
                    OLD.empl_name_last, OLD.empl_name_midle, OLD.empl_name_first,
                    OLD.empl_kana_last, OLD.empl_kana_midle, OLD.empl_kana_first,
                    OLD.empl_alpha_last, OLD.empl_alpha_midle, OLD.empl_alpha_first,
                    OLD.empl_email, OLD.empl_mobile, OLD.empl_notes);
        END');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('xlog_employees');
    }
};
