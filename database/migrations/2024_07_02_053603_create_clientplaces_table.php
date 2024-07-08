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
        Schema::create('clientplaces', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cl')->comment('顧客ID');
            $table->string('cl_pl_cd', 8)->comment('事業所コード');
            $table->string('cl_pl_name')->comment('事業所名');
            $table->string('cl_pl_kana')->comment('事業所名カナ');
            $table->string('cl_pl_alpha')->comment('事業所名英字');
            $table->string('cl_pl_notes')->nullable()->comment('備考');
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();

            $table->unique(['id_cl', 'cl_pl_cd']);
        });

        Schema::create('xlog_clientplaces', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_cl_pl');
            $table->bigInteger('id_cl');
            $table->string('cl_pl_cd', 8);
            $table->string('cl_pl_name');
            $table->string('cl_pl_kana');
            $table->string('cl_pl_alpha');
            $table->string('cl_pl_notes')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_clientplaces_update AFTER UPDATE ON `clientplaces` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clientplaces`
                    (`opr`, `id_cl_pl`, `id_cl`, `cl_pl_cd`, `cl_pl_name`, `cl_pl_kana`, `cl_pl_alpha`, `cl_pl_notes`)
                VALUES 
                    ("U", OLD.id, OLD.id_cl, OLD.cl_pl_cd, OLD.cl_pl_name, OLD.cl_pl_kana, OLD.cl_pl_alpha, OLD.cl_pl_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_clientplaces_delete AFTER DELETE ON `clientplaces` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_clientplaces`
                    (`opr`, `id_cl_pl`, `id_cl`, `cl_pl_cd`, `cl_pl_name`, `cl_pl_kana`, `cl_pl_alpha`, `cl_pl_notes`)
                VALUES 
                    ("D", OLD.id, OLD.id_cl, OLD.cl_pl_cd, OLD.cl_pl_name, OLD.cl_pl_kana, OLD.cl_pl_alpha, OLD.cl_pl_notes);
            END');

        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientplaces');
        Schema::dropIfExists('xlog_clientplaces');
    }
};
