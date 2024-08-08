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
        Schema::create('closepayrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('work_year')->comment('年');
            $table->unsignedTinyInteger('work_month')->comment('月');
            $table->unsignedTinyInteger('closed')->default(0)->comment('締め済み');
            $table->dateTime('operation_date')->nullable()->comment('処理日');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_closepayrolls', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('closepayroll_id');
            $table->unsignedInteger('work_year');
            $table->unsignedTinyInteger('work_month');
            $table->unsignedTinyInteger('closed');
            $table->dateTime('operation_date')->nullable();
            $table->string('notes')->nullable();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_delete');
        
        DB::unprepared('CREATE TRIGGER trg_closepayrolls_update AFTER UPDATE ON `closepayrolls` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_closepayrolls`
                    (`opr`, `closepayroll_id`, `work_year`, `work_month`, `closed`, `operation_date`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.work_year, OLD.work_month, OLD.closed, OLD.operation_date, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_closepayrolls_delete AFTER DELETE ON `closepayrolls` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_closepayrolls`
                    (`opr`, `closepayroll_id`, `work_year`, `work_month`, `closed`, `operation_date`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.work_year, OLD.work_month, OLD.closed, OLD.operation_date, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closepayrolls');
        Schema::dropIfExists('xlog_closepayrolls');
    }
};
