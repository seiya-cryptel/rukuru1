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
            $table->string('empl_sex', 4)->nullable()->comment('性別')->after('empl_alpha_first');
            $table->dateTime('empl_hire_date')->nullable()->comment('入社日')->after('empl_mobile');
            $table->dateTime('empl_resign_date')->nullable()->comment('退社日')->after('empl_hire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('empl_sex');
            $table->dropColumn('empl_hire_date');
            $table->dropColumn('empl_resign_date');
        });
    }
};
