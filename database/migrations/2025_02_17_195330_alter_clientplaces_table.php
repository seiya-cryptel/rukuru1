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
        Schema::table('clientplaces', function (Blueprint $table) {
            $table->string('cl_pl_kana')->nullable()->comment('部門名カナ')->change();
            $table->string('cl_pl_alpha')->nullable()->comment('部門名英字')->change();
        });
        Schema::table('xlog_clientplaces', function (Blueprint $table) {
            $table->string('cl_pl_kana')->nullable()->comment('部門名カナ')->change();
            $table->string('cl_pl_alpha')->nullable()->comment('部門名英字')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientplaces', function (Blueprint $table) {
            $table->string('cl_pl_kana')->nullable(false)->comment('部門名カナ')->change();
            $table->string('cl_pl_alpha')->nullable(false)->comment('部門名英字')->change();
        });
        Schema::table('xlog_clientplaces', function (Blueprint $table) {
            $table->string('cl_pl_kana')->nullable(false)->comment('部門名カナ')->change();
            $table->string('cl_pl_alpha')->nullable(false)->comment('部門名英字')->change();
        });
    }
};
