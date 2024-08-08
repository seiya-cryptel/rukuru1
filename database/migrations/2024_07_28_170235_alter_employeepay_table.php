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
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->unsignedBigInteger('clientplace_id')->nullable()->change();
        });

        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_wt')->change();
            $table->unsignedBigInteger('id_cl')->nullable()->change();
            $table->unsignedBigInteger('id_cl_pl')->nullable()->change();
            $table->renameColumn('id_cl', 'client_id');
            $table->renameColumn('id_cl_pl', 'clientplace_id');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientworktypes_delete');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_update AFTER UPDATE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, `wt_notes`)
            VALUES 
                ("U", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, OLD.wt_notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_clientworktypes_delete AFTER DELETE ON `clientworktypes` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_clientworktypes`
                (`opr`, `id_wt`, `client_id`, `clientplace_id`, `wt_cd`, `wt_name`, `wt_kana`, `wt_alpha`, `wt_notes`)
            VALUES 
                ("D", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.wt_name, OLD.wt_kana, OLD.wt_alpha, OLD.wt_notes);
        END');

        Schema::table('xlog_employeepays', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl_pay')->change();
            $table->unsignedBigInteger('id_empl')->change();
            $table->unsignedBigInteger('id_cl_pl')->nullable()->change();

            $table->unsignedBigInteger('client_id')->nullable()->comment('顧客ID')->after('id_empl');
            $table->decimal('billhour', 12,4)->default(0)->comment('請求単価')->after('payhour');

            $table->decimal('payhour', 12,4)->default(0)->comment('時給')->change();

            $table->renameColumn('id_empl_pay', 'employeepay_id');
            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('id_cl_pl', 'clientplace_id');
        });


        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_delete');

        DB::unprepared('CREATE TRIGGER trg_employeepays_update AFTER UPDATE ON `employeepays` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeepays`
                    (`opr`, `employeepay_id`, `employee_id`, `client_id`, `clientplace_id`, `wt_cd`, `payhour`, `billhour`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.employee_id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.payhour, OLD.billhour, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_employeepays_delete AFTER DELETE ON `employeepays` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_employeepays`
                    (`opr`, `employeepay_id`, `employee_id`, `client_id`, `clientplace_id`, `wt_cd`, `payhour`, `billhour`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.employee_id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, OLD.payhour, OLD.billhour, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xlog_employeepays', function (Blueprint $table) {
            $table->renameColumn('employeepay_id', 'id_empl_pay');
            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('clientplace_id', 'id_cl_pl');
            $table->dropColumn('client_id');
            $table->dropColumn('billhour');
        });
    }
};
