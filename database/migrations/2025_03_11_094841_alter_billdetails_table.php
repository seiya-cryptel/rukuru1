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
        Schema::table('billdetails', function (Blueprint $table) {
            $table->string('quantity_string')->nullable()->comment('数量文字列')->after('quantity');
        });

        Schema::table('xlog_billdetails', function (Blueprint $table) {
            $table->string('quantity_string')->nullable()->comment('数量文字列')->after('quantity');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_delete');

        DB::unprepared('CREATE TRIGGER trg_billdetails_update AFTER UPDATE ON `billdetails` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_billdetails`
                    (`opr`, `billdetail_id`, `bill_id`, 
                    `display_order`, `title`, `unit_price`, `quantity`, `quantity_string`, `unit`, 
                    `amount`, `tax`, `total`, `notes`)
                VALUES 
                    ("U", OLD.id, OLD.bill_id,
                    OLD.display_order, OLD.title, OLD.unit_price, OLD.quantity, OLD.quantity_string, OLD.unit, 
                    OLD.amount, OLD.tax, OLD.total, OLD.notes);
            END');
        
        DB::unprepared('CREATE TRIGGER trg_billdetails_delete AFTER DELETE ON `billdetails` FOR EACH ROW
            BEGIN
                INSERT INTO `xlog_billdetails`
                    (`opr`, `billdetail_id`, `bill_id`, 
                    `display_order`, `title`, `unit_price`, `quantity`, `quantity_string`, `unit`, 
                    `amount`, `tax`, `total`, `notes`)
                VALUES 
                    ("D", OLD.id, OLD.bill_id,
                    OLD.display_order, OLD.title, OLD.unit_price, OLD.quantity, OLD.quantity_string, OLD.unit, 
                    OLD.amount, OLD.tax, OLD.total, OLD.notes);
            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_billdetails_delete');

        Schema::table('billdetails', function (Blueprint $table) {
            $table->dropColumn('quantity_string');
        });
        Schema::table('xlog_billdetails', function (Blueprint $table) {
            $table->dropColumn('quantity_string');
        });
    }
};
