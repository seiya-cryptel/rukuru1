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
        DB::unprepared('DROP TRIGGER IF EXISTS trg_masterallowdeducts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_masterallowdeducts_delete');
        
        DB::unprepared('CREATE TRIGGER trg_masterallowdeducts_update AFTER UPDATE ON `masterallowdeducts` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_masterallowdeducts`
                    (`opr`, `id_mad`, `mad_cd`, `mad_allow`, `mad_deduct`, `mad_name`, `mad_notes`) 
                VALUES 
                    ("U", OLD.id, OLD.mad_cd, OLD.mad_allow, OLD.mad_deduct, OLD.mad_name, OLD.mad_notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_masterallowdeducts_delete AFTER DELETE ON `masterallowdeducts` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_masterallowdeducts`
                (`opr`, `id_mad`, `mad_cd`, `mad_allow`, `mad_deduct`, `mad_name`, `mad_notes`) 
            VALUES 
                ("D", OLD.id, OLD.mad_cd, OLD.mad_allow, OLD.mad_deduct, OLD.mad_name, OLD.mad_notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_masterallowdeducts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_masterallowdeducts_delete');
    }
};
