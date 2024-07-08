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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('cl_cd', 8)->unique()->comment('顧客コード');
            $table->string('cl_name')->comment('顧客名');
            $table->string('cl_kana')->comment('顧客名カナ');
            $table->string('cl_alpha')->comment('顧客名英字');
            $table->string('cl_zip', 8)->nullable()->comment('郵便番号');
            $table->string('cl_addr1')->nullable()->comment('住所1');
            $table->string('cl_addr2')->nullable()->comment('住所2');
            $table->string('cl_psn_div')->nullable()->comment('連絡先部署');
            $table->string('cl_psn_title')->nullable()->comment('連絡先肩書');
            $table->string('cl_psn_name')->nullable()->comment('連絡先氏名');
            $table->string('cl_psn_kana')->nullable()->comment('連絡先氏名カナ');
            $table->string('cl_psn_mail')->nullable()->comment('連絡先メール');
            $table->string('cl_psn_tel')->nullable()->comment('連絡先電話');
            $table->string('cl_psn_fax')->nullable()->comment('連絡先Fax');
            $table->string('cl_notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_clients', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_cl');
            $table->string('cl_cd', 8);
            $table->string('cl_name');
            $table->string('cl_kana');
            $table->string('cl_alpha');
            $table->string('cl_zip', 8)->nullable();
            $table->string('cl_addr1')->nullable();
            $table->string('cl_addr2')->nullable();
            $table->string('cl_psn_div')->nullable();
            $table->string('cl_psn_title')->nullable();
            $table->string('cl_psn_name')->nullable();
            $table->string('cl_psn_kana')->nullable();
            $table->string('cl_psn_mail')->nullable();
            $table->string('cl_psn_tel')->nullable();
            $table->string('cl_psn_fax')->nullable();
            $table->string('cl_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_clients_update AFTER UPDATE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.cl_cd, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax, 
                        OLD.cl_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_clients_delete AFTER DELETE ON `clients` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clients`
                    (`opr`, `id_cl`, `cl_cd`, `cl_name`, `cl_kana`, `cl_alpha`, `cl_zip`, `cl_addr1`, `cl_addr2`, 
                        `cl_psn_div`, `cl_psn_title`, `cl_psn_name`, `cl_psn_kana`, `cl_psn_mail`, `cl_psn_tel`, `cl_psn_fax`, 
                        `cl_notes`) 
                VALUES 
                    ("D", OLD.id, OLD.cl_cd, OLD.cl_name, OLD.cl_kana, OLD.cl_alpha, OLD.cl_zip, OLD.cl_addr1, OLD.cl_addr2, 
                        OLD.cl_psn_div, OLD.cl_psn_title, OLD.cl_psn_name, OLD.cl_psn_kana, OLD.cl_psn_mail, OLD.cl_psn_tel, OLD.cl_psn_fax, 
                        OLD.cl_notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('xlog_clients');
    }
};
