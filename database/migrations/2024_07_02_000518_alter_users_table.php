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
        Schema::create('xlog_users', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);
            $table->bigInteger('id_user');
            $table->string('name');
            $table->string('email');
            $table->dateTime('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
        });

        DB::unprepared('CREATE TRIGGER trg_users_update AFTER UPDATE ON `users` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_users`
                    (`opr`, `id_user`, `name`, `email`, `email_verified_at`, `password`, `remember_token`) 
                VALUES 
                    ("U", OLD.id, OLD.name, OLD.email, OLD.email_verified_at, OLD.password, OLD.remember_token);
            END');

        DB::unprepared('CREATE TRIGGER trg_users_delete AFTER DELETE ON `users` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_users`
                    (`opr`, `id_user`, `name`, `email`, `email_verified_at`, `password`, `remember_token`) 
                VALUES 
                    ("D", OLD.id, OLD.name, OLD.email, OLD.email_verified_at, OLD.password, OLD.remember_token);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xlog_users');
    }
};
