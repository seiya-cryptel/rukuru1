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
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('empl_name_midle', 'empl_name_middle');
            $table->renameColumn('empl_kana_midle', 'empl_kana_middle');
            $table->renameColumn('empl_alpha_midle', 'empl_alpha_middle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('empl_name_middle', 'empl_name_midle');
            $table->renameColumn('empl_kana_middle', 'empl_kana_midle');
            $table->renameColumn('empl_alpha_middle', 'empl_alpha_midle');
        });
    }
};
