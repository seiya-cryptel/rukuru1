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
        Schema::create('employeepays', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_empl')->comment('従業員ID');
            $table->bigInteger('id_cl_pl')->default(0)->comment('事業所ID');
            $table->string('wt_cd', 8)->default('N')->comment('作業種類コード');
            $table->decimal('payhour', 12, 4)->comment('時給');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_employeepays', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_empl_pay');
            $table->bigInteger('id_empl');
            $table->bigInteger('id_cl_pl')->nullable();
            $table->string('wt_cd', 8);
            $table->decimal('payhour', 12, 4);
            $table->string('notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_employeepays_update AFTER UPDATE ON `employeepays` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeepays`
                    (`opr`, `id_empl_pay`, `id_empl`, `id_cl_pl`, `wt_cd`, `payhour`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.id_empl, OLD.id_cl_pl, OLD.wt_cd, OLD.payhour, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_employeepays_delete AFTER DELETE ON `employeepays` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeepays`
                    (`opr`, `id_empl_pay`, `id_empl`, `id_cl_pl`, `wt_cd`, `payhour`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.id_empl, OLD.id_cl_pl, OLD.wt_cd, OLD.payhour, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeepays');
        Schema::dropIfExists('xlog_employeepays');
    }
};
