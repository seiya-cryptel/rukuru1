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
        Schema::create('employeeworks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_empl')->comment('従業員ID');
            $table->dateTime('wrk_date')->comment('勤怠日付');
            $table->tinyInteger('wrk_seq')->default(1)->comment('勤怠連番');
            $table->tinyInteger('leave')->nullable()->comment('休暇フラグ');
            $table->bigInteger('id_cl_pl')->comment('事業所ID');
            $table->string('wt_cd', 8)->default('N')->comment('作業種類コード');
            $table->time('wrk_log_start')->nullable()->comment('打刻開始');
            $table->time('wrk_log_end')->nullable()->comment('打刻終了');
            $table->time('wrk_work_start')->nullable()->comment('勤怠開始');
            $table->time('wrk_work_end')->nullable()->comment('勤怠終了');
            $table->decimal('wrk_work_hours', 12, 4)->nullable()->comment('勤務時間数');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_employeeworks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_empl_wrk');
            $table->bigInteger('id_empl');
            $table->dateTime('wrk_date');
            $table->tinyInteger('wrk_seq')->default(1);
            $table->tinyInteger('leave')->nullable();
            $table->bigInteger('id_cl_pl');
            $table->string('wt_cd', 8)->default('N');
            $table->time('wrk_log_start')->nullable();
            $table->time('wrk_log_end')->nullable();
            $table->time('wrk_work_start')->nullable();
            $table->time('wrk_work_end')->nullable();
            $table->decimal('wrk_work_hours', 12, 4)->nullable();
            $table->string('notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_employeeworks_update AFTER UPDATE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `id_empl_wrk`, `id_empl`, `wrk_date`, `wrk_seq`, `leave`, `id_cl_pl`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, 
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.id_empl, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.id_cl_pl, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, 
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeeworks_delete AFTER DELETE ON `employeeworks` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeeworks`
                (`opr`, `id_empl_wrk`, `id_empl`, `wrk_date`, `wrk_seq`, `leave`, `id_cl_pl`, `wt_cd`, 
                `wrk_log_start`, `wrk_log_end`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, 
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.id_empl, OLD.wrk_date, OLD.wrk_seq, OLD.leave, OLD.id_cl_pl, OLD.wt_cd, 
                OLD.wrk_log_start, OLD.wrk_log_end, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, 
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeeworks');
        Schema::dropIfExists('xlog_employeeworks');
    }
};
