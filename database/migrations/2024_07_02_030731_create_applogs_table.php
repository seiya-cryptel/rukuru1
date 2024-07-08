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
        Schema::create('applogs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent()->comment('ログ日時');
            $table->tinyInteger('log_type')->default(0)->comment('ログ種別');
            $table->string('log_message', 4000)->nullable()->comment('ログメッセージ');
            $table->string('remote_addr')->nullable()->comment('クライアント アドレス');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applogs');
    }
};
