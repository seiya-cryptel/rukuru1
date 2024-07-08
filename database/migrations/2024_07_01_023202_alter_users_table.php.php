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
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('email_verified_at')->nullable()->change();
            $table->dateTime('updated_at')->useCurrent()->nullable()->change();
            $table->dateTime('created_at')->useCurrent()->nullable()->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dateTime('created_at')->useCurrent()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->timestamp('updated_at')->useCurrent()->nullable()->change();
            $table->timestamp('created_at')->useCurrent()->nullable()->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->nullable()->change();
        });
    }
};
