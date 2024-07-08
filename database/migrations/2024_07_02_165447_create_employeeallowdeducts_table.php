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
        Schema::create('employeeallowdeducts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_empl')->comment('従業員ID');
            $table->integer('wrk_year')->comment('勤怠年');
            $table->integer('wrk_month')->comment('勤怠月');
            $table->string('mad_cd', 8)->comment('控除手当コード');
            $table->tinyInteger('mad_deduct')->default(0)->comment('控除フラグ');
            $table->decimal('amount', 12, 4)->comment('金額');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_employeeallowdeducts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_empl_ad');
            $table->bigInteger('id_empl');
            $table->integer('wrk_year');
            $table->integer('wrk_month');
            $table->string('mad_cd', 8);
            $table->tinyInteger('mad_deduct');
            $table->decimal('amount', 12, 4);
            $table->string('notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_update AFTER UPDATE ON `employeeallowdeducts` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeeallowdeducts`
                    (`opr`, `id_empl_ad`, `id_empl`, 
                    `wrk_year`, `wrk_month`, `mad_cd`, `mad_deduct`, `amount`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.id_empl, 
                    OLD.wrk_year, OLD.wrk_month, OLD.mad_cd, OLD.mad_deduct, OLD.amount, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_employeeallowdeducts_delete AFTER DELETE ON `employeeallowdeducts` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeeallowdeducts`
                    (`opr`, `id_empl_ad`, `id_empl`, 
                    `wrk_year`, `wrk_month`, `mad_cd`, `mad_deduct`, `amount`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.id_empl, 
                    OLD.wrk_year, OLD.wrk_month, OLD.mad_cd, OLD.mad_deduct, OLD.amount, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeeallowdeducts');
        Schema::dropIfExists('xlog_employeeallowdeducts');
    }
};
