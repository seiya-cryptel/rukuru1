<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 勤務体系マスタ
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('worktypes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('worktype_kintai')->defalut(0)->comment('勤怠入力種別');
            $table->unsignedTinyInteger('worktype_cd')->defalut(0)->comment('勤務体系コード');
            $table->string('worktype_name', 256)->comment('勤務体系名');
            $table->unsignedTinyInteger('worktype_time_spec')->defalut(0)->comment('体系区分');
            $table->time('worktype_time_start')->nullable()->comment('始業時刻');
            $table->time('worktype_time_end')->nullable()->comment('終業時刻');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('updated_at')->useCurrent()->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable();

            $table->unique(['worktype_kintai', 'worktype_cd']);
            });

        Schema::create('xlog_worktypes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('worktype_id');
            $table->unsignedTinyInteger('worktype_kintai')->defalut(0)->comment('勤怠入力種別');
            $table->unsignedTinyInteger('worktype_cd')->defalut(0)->comment('勤務体系コード');
            $table->string('worktype_name', 256)->comment('勤務体系名');
            $table->unsignedTinyInteger('worktype_time_spec')->defalut(0)->comment('体系区分');
            $table->time('worktype_time_start')->nullable()->comment('始業時刻');
            $table->time('worktype_time_end')->nullable()->comment('終業時刻');
            $table->string('notes')->nullable()->comment('備考');
            });

        DB::unprepared('CREATE TRIGGER trg_worktypes_update AFTER UPDATE ON `worktypes` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_worktypes`
                    (`opr`, `worktype_id`, `worktype_kintai`, `worktype_cd`, `worktype_name`, 
                    `worktype_time_spec`, `worktype_time_start`, `worktype_time_end`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.worktype_kintai, OLD.worktype_cd, OLD.worktype_name, 
                    OLD.worktype_time_spec, OLD.worktype_time_start, OLD.worktype_time_end, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_worktypes_delete AFTER DELETE ON `worktypes` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_worktypes`
                    (`opr`, `worktype_id`, `worktype_kintai`, `worktype_cd`, `worktype_name`, 
                    `worktype_time_spec`, `worktype_time_start`, `worktype_time_end`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.worktype_kintai, OLD.worktype_cd, OLD.worktype_name, 
                    OLD.worktype_time_spec, OLD.worktype_time_start, OLD.worktype_time_end, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_delete');

        Schema::dropIfExists('worktypes');
        Schema::dropIfExists('xlog_worktypes');
    }
};
