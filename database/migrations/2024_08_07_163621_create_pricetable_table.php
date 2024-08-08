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
        Schema::create('pricetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->comment('顧客ID');
            $table->unsignedBigInteger('clientplace_id')->comment('事業所ID');
            $table->string('wt_cd', 8)->default('N')->comment('作業種類コード');
            $table->string('bill_name')->comment('請求項目名');
            $table->string('bill_print_name')->comment('請求書記載名');
            $table->decimal('bill_unitprice', 12, 4)->comment('請求単価');
            $table->unsignedInteger('display_order')->default(1)->comment('表示順');
            $table->string('notes')->comment('備考');

            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });

        Schema::create('xlog_pricetables', function (Blueprint $table) {
            $table->id();
            $table->dateTime('logged_at')->useCurrent();
            $table->string('opr', 1);

            $table->unsignedBigInteger('pricetable_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('clientplace_id');
            $table->string('wt_cd', 8)->default('N');
            $table->string('bill_name');
            $table->string('bill_print_name');
            $table->decimal('bill_unitprice', 12, 4);
            $table->unsignedInteger('display_order')->default(1);
            $table->string('notes');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_pricetables_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pricetables_delete');
        
        DB::unprepared('CREATE TRIGGER trg_pricetables_update AFTER UPDATE ON `pricetables` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_pricetables`
                    (`opr`, `pricetable_id`, `client_id`, `clientplace_id`, `wt_cd`, 
                        `bill_name`, `bill_print_name`, `bill_unitprice`, `display_order`, `notes`) 
                VALUES 
                    ("U", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, 
                        OLD.bill_name, OLD.bill_print_name, OLD.bill_unitprice, OLD.display_order, OLD.notes);
            END');
        
        DB::unprepared('CREATE TRIGGER trg_pricetables_delete AFTER DELETE ON `pricetables` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_pricetables`
                    (`opr`, `pricetable_id`, `client_id`, `clientplace_id`, `wt_cd`, 
                        `bill_name`, `bill_print_name`, `bill_unitprice`, `display_order`, `notes`) 
                VALUES 
                    ("D", OLD.id, OLD.client_id, OLD.clientplace_id, OLD.wt_cd, 
                        OLD.bill_name, OLD.bill_print_name, OLD.bill_unitprice, OLD.display_order, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricetables');
        Schema::dropIfExists('xlog_pricetables');
    }
};
