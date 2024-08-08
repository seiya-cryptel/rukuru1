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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no', 8)->nullable()->comment('請求書番号');
            $table->dateTime('bill_date')->nullable()->comment('請求日');
            $table->unsignedBigInteger('client_id')->comment('顧客ID');
            $table->unsignedBigInteger('clientplace_id')->comment('事業所ID');
            $table->unsignedInteger('work_year')->comment('対象年');
            $table->unsignedTinyInteger('work_month')->comment('対象月');
            $table->string('bill_title', 8)->comment('請求件名');
            $table->decimal('bill_amount', 12, 4)->comment('税別請求金額');
            $table->decimal('bill_tax', 12, 4)->comment('消費税');
            $table->decimal('bill_total', 12, 4)->comment('税込請求金額');
            $table->string('notes')->nullable()->comment('備考');

            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_bills', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);

            $table->bigInteger('bill_id');
            $table->string('bill_no', 8)->nullable()->comment('請求書番号');
            $table->dateTime('bill_date')->nullable()->comment('請求日');
            $table->unsignedBigInteger('clientplace_id')->comment('事業所ID');
            $table->unsignedBigInteger('client_id')->comment('顧客ID');
            $table->unsignedInteger('work_year')->comment('対象年');
            $table->unsignedTinyInteger('work_month')->comment('対象月');
            $table->string('bill_title', 8)->comment('請求件名');
            $table->decimal('bill_amount', 12, 4)->comment('税別請求金額');
            $table->decimal('bill_tax', 12, 4)->comment('消費税');
            $table->decimal('bill_total', 12, 4)->comment('税込請求金額');
            $table->string('notes')->nullable()->comment('備考');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_bills_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_bills_delete');
        
        DB::unprepared('CREATE TRIGGER trg_bills_update AFTER UPDATE ON `bills` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_bills`
                    (`opr`, `bill_id`, `bill_no`, `bill_date`, `client_id`, `clientplace_id`, 
                    `work_year`, `work_month`, 
                    `bill_title`, `bill_amount`, `bill_tax`, `bill_total`, `notes`)
                VALUES 
                    ("U", OLD.id, OLD.bill_no, OLD.bill_date, OLD.client_id, OLD.clientplace_id, 
                    OLD.work_year, OLD.work_month, 
                    OLD.bill_title, OLD.bill_amount, OLD.bill_tax, OLD.bill_total, OLD.notes);
            END');
        
        DB::unprepared('CREATE TRIGGER trg_bills_delete AFTER DELETE ON `bills` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_bills`
                    (`opr`, `bill_id`, `bill_no`, `bill_date`, `client_id`, `clientplace_id`, 
                    `work_year`, `work_month`, 
                    `bill_title`, `bill_amount`, `bill_tax`, `bill_total`, `notes`)
                VALUES 
                    ("D", OLD.id, OLD.bill_no, OLD.bill_date, OLD.client_id, OLD.clientplace_id, 
                    OLD.work_year, OLD.work_month, 
                    OLD.bill_title, OLD.bill_amount, OLD.bill_tax, OLD.bill_total, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
        Schema::dropIfExists('xlog_bills');
    }
};
