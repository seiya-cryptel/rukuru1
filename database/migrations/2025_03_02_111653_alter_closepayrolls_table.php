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
        Schema::table('closepayrolls', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->comment('顧客ID')->after('work_month');
        });
        Schema::table('xlog_closepayrolls', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->comment('顧客ID')->after('work_month');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_delete');
        
        DB::unprepared('CREATE TRIGGER trg_closepayrolls_update AFTER UPDATE ON `closepayrolls` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_closepayrolls`
                    (`opr`, `closepayroll_id`, `work_year`, `work_month`, `client_id`, `closed`, `operation_date`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.work_year, OLD.work_month, OLD.client_id, OLD.closed, OLD.operation_date, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_closepayrolls_delete AFTER DELETE ON `closepayrolls` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_closepayrolls`
                    (`opr`, `closepayroll_id`, `work_year`, `work_month`, `client_id`, `closed`, `operation_date`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.work_year, OLD.work_month, OLD.client_id, OLD.closed, OLD.operation_date, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_closepayrolls_delete');

        Schema::table('closepayrolls', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });

        Schema::table('xlog_closepayrolls', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
};
