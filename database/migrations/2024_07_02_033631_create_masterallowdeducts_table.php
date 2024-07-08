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
        Schema::create('masterallowdeducts', function (Blueprint $table) {
            $table->id();
            $table->string('mad_cd', 8)->unique()->comment('手当控除項目コード');
            $table->tinyInteger('mad_allow')->default(0)->comment('手当フラグ');
            $table->tinyInteger('mad_deduct')->default(0)->comment('控除フラグ');
            $table->string('mad_name')->comment('手当控除項目名称');
            $table->string('mad_notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_masterallowdeducts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_mad');
            $table->string('mad_cd', 8);
            $table->tinyInteger('mad_allow');
            $table->tinyInteger('mad_deduct');
            $table->string('mad_name');
            $table->string('mad_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_masterallowdeducts_update AFTER UPDATE ON `masterallowdeducts` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_masterallowdeducts`
                    (`opr`, `id_mad`, `mad_cd`, `mad_allow`, `mad_deduct`, `mad_name`, `md_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.mad_cd, OLD.mad_allow, OLD.mad_deduct, OLD.mad_name, OLD.mad_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_masterallowdeducts_delete AFTER DELETE ON `masterallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_masterallowdeducts`
                (`opr`, `id_mad`, `mad_cd`, `mad_allow`, `mad_deduct`, `mad_name`, `md_notes`) 
            VALUES 
                ("D", OLD.id, OLD.mad_cd, OLD.mad_allow, OLD.mad_deduct, OLD.mad_name, OLD.mad_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masterallowdeducts');
        Schema::dropIfExists('xlog_masterallowdeducts');
    }
};
