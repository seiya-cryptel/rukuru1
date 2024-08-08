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
        Schema::table('employeepays', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empl')->change();
            $table->unsignedBigInteger('id_cl_pl')->nullable()->change();

            $table->unsignedBigInteger('client_id')->nullable()->comment('顧客ID')->after('id_empl');
            $table->decimal('billhour', 12,4)->default(0)->comment('請求単価')->after('payhour');

            $table->decimal('payhour', 12,4)->default(0)->comment('時給')->change();

            $table->renameColumn('id_empl', 'employee_id');
            $table->renameColumn('id_cl_pl', 'clientplace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeepays', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'id_empl');
            $table->renameColumn('clientplace_id', 'id_cl_pl');
            $table->dropColumn('client_id');
            $table->dropColumn('billhour');
        });
    }
};
