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
        Schema::create('employeesalary', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_empl')->comment('従業員ID');
            $table->dateTime('wrk_date')->comment('勤怠日付');
            $table->tinyInteger('wrk_seq')->default(1)->comment('勤怠連番');
            $table->time('wrk_work_start')->comment('勤怠開始');
            $table->time('wrk_work_end')->comment('勤怠終了');
            $table->decimal('wrk_work_hours', 12, 4)->comment('勤務時間数');
            $table->decimal('payhour', 12, 4)->comment('時給');
            $table->decimal('premium', 12, 4)->default(1)->comment('割増率');
            $table->decimal('wrk_pay', 12, 4)->default(0)->comment('金額');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_employeesalary', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_empl_slry');
            $table->bigInteger('id_empl');
            $table->dateTime('wrk_date');
            $table->tinyInteger('wrk_seq')->default(1);
            $table->time('wrk_work_start');
            $table->time('wrk_work_end');
            $table->decimal('wrk_work_hours', 12, 4);
            $table->decimal('payhour', 12, 4);
            $table->decimal('premium', 12, 4);
            $table->decimal('wrk_pay', 12, 4)->default(0);
            $table->string('notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_employeesalary_update AFTER UPDATE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `id_empl_slry`, `id_empl`, `wrk_date`, `wrk_seq`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.id_empl, OLD.wrk_date, OLD.wrk_seq, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeesalary_delete AFTER DELETE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `id_empl_slry`, `id_empl`, `wrk_date`, `wrk_seq`, 
                `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.id_empl, OLD.wrk_date, OLD.wrk_seq, 
                OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeesalary');
        Schema::dropIfExists('xlog_employeesalary');
    }
};
