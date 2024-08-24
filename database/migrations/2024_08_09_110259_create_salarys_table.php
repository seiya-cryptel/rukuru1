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
        Schema::create('salarys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('従業員ID');
            $table->unsignedInteger('work_year')->comment('対象年');
            $table->unsignedTinyInteger('work_month')->comment('対象月');
            $table->decimal('work_amount', 12, 0)->comment('勤怠合計額');
            $table->decimal('allow_amount', 12, 0)->comment('手当合計額');
            $table->decimal('deduct_amount', 12, 0)->comment('控除合計額');
            $table->decimal('transport', 12, 0)->comment('交通費');
            $table->decimal('pay_amount', 12, 0)->comment('支給額');
            $table->string('notes')->nullable()->comment('備考');

            $table->dateTime('updated_at')->useCurrent()->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable();

            $table->unique(['employee_id', 'work_year', 'work_month']);
        });

        Schema::create('xlog_salarys', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);

            $table->unsignedBigInteger('salary_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('work_year');
            $table->unsignedTinyInteger('work_month');
            $table->decimal('work_amount', 12, 0);
            $table->decimal('allow_amount', 12, 0);
            $table->decimal('deduct_amount', 12, 0);
            $table->decimal('transport', 12, 0);
            $table->decimal('pay_amount', 12, 0);
            $table->string('notes')->nullable();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_salarys_delete');

        DB::unprepared('CREATE TRIGGER trg_salarys_update AFTER UPDATE ON `salarys` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_salarys`
                    (`opr`, `salary_id`, `employee_id`, `work_year`, `work_month`, 
                    `work_amount`, `allow_amount`, `deduct_amount`, `transport`, `pay_amount`, `notes`)
                VALUES 
                    ("U", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, 
                    OLD.work_amount, OLD.allow_amount, OLD.deduct_amount, OLD.transport, OLD.pay_amount, OLD.notes);
            END');

        DB::unprepared('CREATE TRIGGER trg_salarys_delete AFTER DELETE ON `salarys` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_salarys`
                    (`opr`, `salary_id`, `employee_id`, `work_year`, `work_month`, 
                    `work_amount`, `allow_amount`, `deduct_amount`, `transport`, `pay_amount`, `notes`)
                VALUES 
                    ("D", OLD.id, OLD.employee_id, OLD.work_year, OLD.work_month, 
                    OLD.work_amount, OLD.allow_amount, OLD.deduct_amount, OLD.transport, OLD.pay_amount, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salarys');
        Schema::dropIfExists('xlog_salarys');
    }
};
