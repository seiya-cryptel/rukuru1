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
        Schema::create('billdetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id')->comment('請求ID');
            $table->unsignedTinyInteger('display_order')->comment('表示順');
            $table->string('title')->comment('明細名');
            $table->decimal('unit_price', 12, 4)->comment('単価');
            $table->decimal('quantity', 12, 4)->comment('数量');
            $table->string('unit')->comment('単位');
            $table->decimal('amount', 12, 4)->comment('税別金額');
            $table->decimal('tax', 12, 4)->comment('消費税');
            $table->decimal('total', 12, 4)->comment('税込金額');
            $table->string('notes')->nullable()->comment('備考');

            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_billdetails', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);

            $table->bigInteger('billdetail_id');
            $table->unsignedBigInteger('bill_id');
            $table->unsignedTinyInteger('display_order');
            $table->string('title');
            $table->decimal('unit_price', 12, 4);
            $table->decimal('quantity', 12, 4);
            $table->string('unit');
            $table->decimal('amount', 12, 4);
            $table->decimal('tax', 12, 4);
            $table->decimal('total', 12, 4);
            $table->string('notes')->nullable();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_delete');
        
        DB::unprepared('CREATE TRIGGER trg_billdetails_update AFTER UPDATE ON `billdetails` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_billdetails`
                    (`opr`, `billdetail_id`, `bill_id`, 
                    `display_order`, `title`, `unit_price`, `quantity`, `unit`, `amount`, `tax`, `total`, `notes`)
                VALUES 
                    ("U", OLD.id, OLD.bill_id,
                    OLD.display_order, OLD.title, OLD.unit_price, OLD.quantity, OLD.unit, OLD.amount, OLD.tax, OLD.total, OLD.notes);
            END');
        
        DB::unprepared('CREATE TRIGGER trg_billdetails_delete AFTER DELETE ON `billdetails` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_billdetails`
                    (`opr`, `billdetail_id`, `bill_id`, 
                    `display_order`, `title`, `unit_price`, `quantity`, `unit`, `amount`, `tax`, `total`, `notes`)
                VALUES 
                    ("D", OLD.id, OLD.bill_id,
                    OLD.display_order, OLD.title, OLD.unit_price, OLD.quantity, OLD.unit, OLD.amount, OLD.tax, OLD.total, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billdetails');
        Schema::dropIfExists('xlog_billdetails');
    }
};
