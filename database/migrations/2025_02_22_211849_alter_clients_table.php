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
            $table->string('cl_full_name', 255)->default('請求先名')->comment('請求先名')->after('cl_cd');
            $table->unsignedTinyInteger('cl_kintai_style')->default(0)->comment('勤怠入力種類')->after('cl_close_day');
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->string('cl_full_name', 255)->comment('請求先名')->after('cl_cd');
            $table->unsignedTinyInteger('cl_kintai_style')->comment('勤怠入力種類')->after('cl_close_day');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_clients_delete');

        DB::unprepared('CREATE TRIGGER trg_clients_update AFTER UPDATE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_full_name`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_dow_statutory`, `cl_dow_non_statutory`, `cl_over_40hpw`, `cl_dow_first`, `cl_round_start`, `cl_round_end`,
                        `cl_close_day`, `cl_kintai_style`,
                        `cl_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.cl_cd, OLD.cl_full_name, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax,
                        OLD.cl_dow_statutory, OLD.cl_dow_non_statutory, OLD.cl_over_40hpw, OLD.cl_dow_first, OLD.cl_round_start, OLD.cl_round_end, 
                        OLD.cl_close_day, OLD.cl_kintai_style,
                        OLD.cl_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_clients_delete AFTER DELETE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_full_name`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_dow_statutory`, `cl_dow_non_statutory`, `cl_over_40hpw`, `cl_dow_first`, `cl_round_start`, `cl_round_end`,
                        `cl_close_day`, `cl_kintai_style`,
                        `cl_notes`) 
                VALUES 
                    ("D", OLD.id, OLD.cl_cd, OLD.cl_full_name, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax,
                        OLD.cl_dow_statutory, OLD.cl_dow_non_statutory, OLD.cl_over_40hpw, OLD.cl_dow_first, OLD.cl_round_start, OLD.cl_round_end, 
                        OLD.cl_close_day, OLD.cl_kintai_style,
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
            $table->dropColumn('cl_full_name');
            $table->dropColumn('cl_kintai_style');
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->dropColumn('cl_full_name');
            $table->dropColumn('cl_kintai_style');
        });
    }
};
