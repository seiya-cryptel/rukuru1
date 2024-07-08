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
        Schema::create('appsettings', function (Blueprint $table) {
            $table->id();
            $table->string('sys_name')->comment('変数名');
            $table->integer('sys_index')->default(0)->comment('変数インデックス');
            $table->tinyInteger('sys_istext')->default(1)->comment('テキスト値フラグ');
            $table->string('sys_txtval')->nullable()->comment('テキスト値');
            $table->decimal('sys_numval', 12, 4)->nullable()->comment('数値');
            $table->string('sys_notes')->nullable()->comment('備考');
            $table->dateTime('updated_at')->useCurrent()->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable();

            $table->unique(['sys_name', 'sys_index']);
        });

        Schema::create('xlog_appsettings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_appsetting');
            $table->string('sys_name');
            $table->integer('sys_index');
            $table->tinyInteger('sys_istext');
            $table->string('sys_txtval')->nullable();
            $table->decimal('sys_numval', 12, 4)->nullable();
            $table->string('sys_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_appsettings_update AFTER UPDATE ON `appsettings` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_appsettings`
                    (`opr`, `id_appsetting`, `sys_name`, `sys_index`, `sys_istext`, `sys_txtval`, `sys_numval`, `sys_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.sys_name, OLD.sys_index, OLD.sys_istext, OLD.sys_txtval, OLD.sys_numval, OLD.sys_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_appsettings_delete AFTER DELETE ON `appsettings` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_appsettings`
                    (`opr`, `id_appsetting`, `sys_name`, `sys_index`, `sys_istext`, `sys_txtval`, `sys_numval`, `sys_notes`) 
                VALUES 
                    ("D", OLD.id, OLD.sys_name, OLD.sys_index, OLD.sys_istext, OLD.sys_txtval, OLD.sys_numval, OLD.sys_notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appsettings');
        Schema::dropIfExists('xlog_appsettings');
    }
};
