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
        Schema::table('bills', function (Blueprint $table) {
            $table->string('bill_title', 255)->comment('請求件名')->change();
        });
        Schema::table('xlog_bills', function (Blueprint $table) {
            $table->string('bill_title', 255)->comment('請求件名')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
