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
        Schema::table('xlog_clientplaces', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cl')->change();

            $table->renameColumn('id_cl', 'client_id');
        });
 
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientplaces_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clientplaces_delete');

        DB::unprepared('CREATE TRIGGER trg_clientplaces_update AFTER UPDATE ON `clientplaces` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clientplaces`
                    (`opr`, `id_cl_pl`, `client_id`, `cl_pl_cd`, `cl_pl_name`, `cl_pl_kana`, `cl_pl_alpha`, `cl_pl_notes`)
                VALUES 
                    ("U", OLD.id, OLD.client_id, OLD.cl_pl_cd, OLD.cl_pl_name, OLD.cl_pl_kana, OLD.cl_pl_alpha, OLD.cl_pl_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_clientplaces_delete AFTER DELETE ON `clientplaces` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clientplaces`
                    (`opr`, `id_cl_pl`, `client_id`, `cl_pl_cd`, `cl_pl_name`, `cl_pl_kana`, `cl_pl_alpha`, `cl_pl_notes`)
                VALUES 
                    ("D", OLD.id, OLD.client_id, OLD.cl_pl_cd, OLD.cl_pl_name, OLD.cl_pl_kana, OLD.cl_pl_alpha, OLD.cl_pl_notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
