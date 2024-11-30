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
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->datetime('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->datetime('wrk_work_end')->nullable()->comment('勤怠終了')->change();
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->datetime('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->datetime('wrk_work_end')->nullable()->comment('勤怠終了')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->time('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->time('wrk_work_end')->nullable()->comment('勤怠終了')->change();
        });

        Schema::table('xlog_employeeworks', function (Blueprint $table) {
            $table->time('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->time('wrk_work_end')->nullable()->comment('勤怠終了')->change();
        });
    }
};
