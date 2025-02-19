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
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->string('wt_kana')->nullable()->comment('業務種別カナ')->change();
            $table->string('wt_alpha')->nullable()->comment('業務種別英字')->change();
        });
        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->string('wt_kana')->nullable()->comment('業務種別カナ')->change();
            $table->string('wt_alpha')->nullable()->comment('業務種別英字')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->string('wt_kana')->nullable(false)->comment('業務種別カナ')->change();
            $table->string('wt_alpha')->nullable(false)->comment('業務種別英字')->change();
        });
        Schema::table('xlog_clientworktypes', function (Blueprint $table) {
            $table->string('wt_kana')->nullable(false)->comment('業務種別カナ')->change();
            $table->string('wt_alpha')->nullable(false)->comment('業務種別英字')->change();
        });
    }
};
