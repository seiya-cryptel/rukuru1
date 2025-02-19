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
            $table->string('cl_kana')->nullable()->comment('顧客名カナ')->change();
            $table->string('cl_alpha')->nullable()->comment('顧客名英字')->change();
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->string('cl_kana')->nullable()->comment('顧客名カナ')->change();
            $table->string('cl_alpha')->nullable()->comment('顧客名英字')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cl_kana')->nullable(false)->comment('顧客名カナ')->change();
            $table->string('cl_alpha')->nullable(false)->comment('顧客名英字')->change();
        });
        Schema::table('xlog_clients', function (Blueprint $table) {
            $table->string('cl_kana')->nullable(false)->comment('顧客名カナ')->change();
            $table->string('cl_alpha')->nullable(false)->comment('顧客名英字')->change();
        });
    }
};
