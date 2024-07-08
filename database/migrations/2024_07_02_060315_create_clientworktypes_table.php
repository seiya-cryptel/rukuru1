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
        Schema::create('clientworktypes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cl')->comment('顧客ID');
            $table->bigInteger('id_cl_pl')->comment('事業所ID');
            $table->string('wt_cd', 8)->default('N')->comment('作業種類コード');
            $table->string('wt_name')->comment('作業種類名');
            $table->string('wt_kana')->comment('作業種類名カナ');
            $table->string('wt_alpha')->comment('作業種類名英字');
            $table->string('wt_notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();

            $table->unique(['id_cl', 'id_cl_pl', 'wt_cd']);
        });

        Schema::create('xlog_clientworktypes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_wt');
            $table->bigInteger('id_cl');
            $table->bigInteger('id_cl_pl');
            $table->string('wt_cd', 8)->default('N');
            $table->string('wt_name');
            $table->string('wt_kana');
            $table->string('wt_alpha');
            $table->string('wt_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_update AFTER UPDATE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `id_cl`, `id_cl_pl`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, `wt_notes`)
            VALUES 
                ("U", OLD.id, OLD.id_cl, OLD.id_cl_pl, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, OLD.wt_notes);
        END');
        
        DB::unprepared('CREATE TRIGGER trg_clientworktypes_delete AFTER DELETE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `id_cl`, `id_cl_pl`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, `wt_notes`)
            VALUES 
                ("D", OLD.id, OLD.id_cl, OLD.id_cl_pl, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, OLD.wt_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientworktypes');
        Schema::dropIfExists('xlog_clientworktypes');
    }
};
