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
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->unique(['employee_id', 'wrk_date', 'wrk_seq', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeeworks', function (Blueprint $table) {
            $table->dropUnique(['employeeworks_employee_id_wrk_date_wrk_seq_client_id_unique']);
        });
    }
};
