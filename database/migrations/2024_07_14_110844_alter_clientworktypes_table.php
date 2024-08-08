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
            $table->unsignedBigInteger('id_cl')->change();
            $table->unsignedBigInteger('id_cl_pl')->change();
            $table->renameColumn('id_cl', 'client_id');
            $table->renameColumn('id_cl_pl', 'clientplace_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('clientplace_id')->references('id')->on('clientplaces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientworktypes', function (Blueprint $table) {
            $table->dropForeign('clientworktypes_client_id_foreign');
            $table->dropForeign('clientworktypes_clientplace_id_foreign');
            $table->renameColumn('client_id', 'id_cl');
            $table->renameColumn('clientplace_id', 'id_cl_pl');
        });
    }
};
