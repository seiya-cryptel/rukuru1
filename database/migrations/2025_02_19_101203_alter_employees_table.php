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
            $table->string('empl_name_first')->nullable()->comment('名')->change();
            $table->string('empl_kana_last')->nullable()->comment('姓カナ')->change();
            $table->string('empl_kana_first')->nullable()->comment('名カナ')->change();
            $table->string('empl_alpha_last')->nullable()->comment('姓英字')->change();
            $table->string('empl_alpha_first')->nullable()->comment('名英字')->change();
        });
        Schema::table('xlog_employees', function (Blueprint $table) {
            $table->string('empl_name_first')->nullable()->comment('名')->change();
            $table->string('empl_kana_last')->nullable()->comment('姓カナ')->change();
            $table->string('empl_kana_first')->nullable()->comment('名カナ')->change();
            $table->string('empl_alpha_last')->nullable()->comment('姓英字')->change();
            $table->string('empl_alpha_first')->nullable()->comment('名英字')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('empl_name_first')->nullable(false)->comment('名')->change();
            $table->string('empl_kana_last')->nullable(false)->comment('姓カナ')->change();
            $table->string('empl_kana_first')->nullable(false)->comment('名カナ')->change();
            $table->string('empl_alpha_last')->nullable(false)->comment('姓英字')->change();
            $table->string('empl_alpha_first')->nullable(false)->comment('名英字')->change();
        });
        Schema::table('xlog_employees', function (Blueprint $table) {
            $table->string('empl_name_first')->nullable(false)->comment('名')->change();
            $table->string('empl_kana_last')->nullable(false)->comment('姓カナ')->change();
            $table->string('empl_kana_first')->nullable(false)->comment('名カナ')->change();
            $table->string('empl_alpha_last')->nullable(false)->comment('姓英字')->change();
            $table->string('empl_alpha_first')->nullable(false)->comment('名英字')->change();
        });
    }
};
