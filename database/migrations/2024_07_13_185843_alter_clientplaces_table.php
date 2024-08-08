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
            $table->unsignedBigInteger('id_cl')->change();
            $table->renameColumn('id_cl', 'client_id');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientplaces', function (Blueprint $table) {
            $table->dropForeign('clientplaces_client_id_foreign');
            $table->renameColumn('client_id', 'id_cl');
        });
    }
};
