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
        Schema::table('employeesalary', function (Blueprint $table) {
            $table->datetime('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->datetime('wrk_work_end')->nullable()->comment('勤怠終了')->change();
            $table->string('wt_bill_item_cd', 8)->nullable()->comment('請求項目コード')->after('wrk_pay');
            $table->string('wt_bill_item_name', 256)->nullable()->comment('請求項目名')->after('wt_bill_item_cd');
            $table->decimal('billhour', 12, 4)->nullable()->comment('請求単価')->after('wt_bill_item_name');
            $table->decimal('billpremium', 12, 4)->nullable()->comment('請求割増率')->after('billhour');
            $table->decimal('wrk_bill', 12, 4)->nullable()->comment('請求金額')->after('billpremium');
        });

        Schema::table('xlog_employeesalary', function (Blueprint $table) {
            $table->datetime('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->datetime('wrk_work_end')->nullable()->comment('勤怠終了')->change();
            $table->string('wt_bill_item_cd', 8)->nullable()->comment('請求項目コード')->after('wrk_pay');
            $table->string('wt_bill_item_name', 256)->nullable()->comment('請求項目名')->after('wt_bill_item_cd');
            $table->decimal('billhour', 12, 4)->nullable()->comment('請求単価')->after('wt_bill_item_name');
            $table->decimal('billpremium', 12, 4)->nullable()->comment('請求割増率')->after('billhour');
            $table->decimal('wrk_bill', 12, 4)->nullable()->comment('請求金額')->after('billpremium');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_delete');

        DB::unprepared('CREATE TRIGGER trg_employeesalary_update AFTER UPDATE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `employeesalary_id`, `employee_id`, `wrk_date`, `wrk_ttl_seq`, `leave`, `client_id`, `clientplace_id`, 
                `wt_cd`, `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `wt_bill_item_cd`, `wt_bill_item_name`, `billhour`, `billpremium`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("U", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_ttl_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.wt_cd, OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.wt_bill_item_cd, OLD.wt_bill_item_name, OLD.billhour, OLD.billpremium, OLD.wrk_bill,
                OLD.notes);
        END');

        DB::unprepared('CREATE TRIGGER trg_employeesalary_delete AFTER DELETE ON `employeesalary` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeesalary`
                (`opr`, `employeesalary_id`, `employee_id`, `wrk_date`, `wrk_ttl_seq`, `leave`, `client_id`, `clientplace_id`, 
                `wt_cd`, `wrk_work_start`, `wrk_work_end`, `wrk_work_hours`, `payhour`, `premium`, `wrk_pay`, 
                `wt_bill_item_cd`, `wt_bill_item_name`, `billhour`, `billpremium`, `wrk_bill`,
                `notes`) 
            VALUES 
                ("D", OLD.id, OLD.employee_id, OLD.wrk_date, OLD.wrk_ttl_seq, OLD.leave, OLD.client_id, OLD.clientplace_id, 
                OLD.wt_cd, OLD.wrk_work_start, OLD.wrk_work_end, OLD.wrk_work_hours, OLD.payhour, OLD.premium, OLD.wrk_pay, 
                OLD.wt_bill_item_cd, OLD.wt_bill_item_name, OLD.billhour, OLD.billpremium, OLD.wrk_bill,
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employeesalary', function (Blueprint $table) {
            $table->time('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->time('wrk_work_end')->nullable()->comment('勤怠終了')->change();
            $table->dropColumn('wt_bill_item_cd');
            $table->dropColumn('wt_bill_item_name');
            $table->dropColumn('billhour');
            $table->dropColumn('billpremium');
            $table->dropColumn('wrk_bill');
        });

        Schema::table('xlog_employeesalary', function (Blueprint $table) {
            $table->time('wrk_work_start')->nullable()->comment('勤怠開始')->change();
            $table->time('wrk_work_end')->nullable()->comment('勤怠終了')->change();
            $table->dropColumn('wt_bill_item_cd');
            $table->dropColumn('wt_bill_item_name');
            $table->dropColumn('billhour');
            $table->dropColumn('billpremium');
            $table->dropColumn('wrk_bill');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeesalary_delete');
    }
};
