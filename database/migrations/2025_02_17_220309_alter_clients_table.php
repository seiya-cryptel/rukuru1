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
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedTinyInteger('cl_close_day')->default(0)->comment('締日')->after('cl_round_end');
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->unsignedTinyInteger('cl_close_day')->default(0)->comment('締日')->after('cl_round_end');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_delete');

        DB::unprepared('CREATE TRIGGER trg_clients_update AFTER UPDATE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_dow_statutory`, `cl_dow_non_statutory`, `cl_over_40hpw`, `cl_dow_first`, `cl_round_start`, `cl_round_end`,
                        `cl_close_day`,
                        `cl_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.cl_cd, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax,
                        OLD.cl_dow_statutory, OLD.cl_dow_non_statutory, OLD.cl_over_40hpw, OLD.cl_dow_first, OLD.cl_round_start, OLD.cl_round_end, 
                        OLD.cl_close_day,
                        OLD.cl_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_clients_delete AFTER DELETE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_dow_statutory`, `cl_dow_non_statutory`, `cl_over_40hpw`, `cl_dow_first`, `cl_round_start`, `cl_round_end`,
                        `cl_close_day`,
                        `cl_notes`) 
                VALUES 
                    ("D", OLD.id, OLD.cl_cd, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax, 
                        OLD.cl_dow_statutory, OLD.cl_dow_non_statutory, OLD.cl_over_40hpw, OLD.cl_dow_first, OLD.cl_round_start, OLD.cl_round_end, 
                        OLD.cl_close_day,
                        OLD.cl_notes);
            END');
     }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_delete');
        
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('cl_close_day');
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->dropColumn('cl_close_day');
        });
    }
};
