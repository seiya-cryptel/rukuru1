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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->dateTime('holiday_date')->comment('祝祭日');
            $table->unsignedBigInteger('client_id')->defalut(0)->comment('限定顧客');
            $table->string('holiday_name', 256)->comment('祝祭日名');
            $table->string('notes')->nullable()->comment('備考');
            $table->dateTime('updated_at')->useCurrent()->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable();

            $table->unique(['holiday_date', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
