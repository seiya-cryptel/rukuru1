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
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_delete');

        DB::unprepared('CREATE TRIGGER trg_employeepays_update AFTER UPDATE ON `employeepays` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeepays`
                (`opr`, `employeepay_id`, `employee_id`, `clientworktype_id`,
                wt_pay_std, wt_pay_ovr, wt_pay_ovr_midnight, wt_pay_holiday, wt_pay_holiday_midnight,
                wt_bill_std, wt_bill_ovr, wt_bill_ovr_midnight, wt_bill_holiday, wt_bill_holiday_midnight,
                notes)
            VALUES
                ("U", OLD.id, OLD.employee_id, OLD.clientworktype_id, 
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight,
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight,
                OLD.notes
                );
        END');

        DB::unprepared('CREATE TRIGGER trg_employeepays_delete AFTER DELETE ON `employeepays` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeepays`
                (`opr`, `employeepay_id`, `employee_id`, `clientworktype_id`,
                wt_pay_std, wt_pay_ovr, wt_pay_ovr_midnight, wt_pay_holiday, wt_pay_holiday_midnight,
                wt_bill_std, wt_bill_ovr, wt_bill_ovr_midnight, wt_bill_holiday, wt_bill_holiday_midnight,
                notes)
            VALUES
                ("D", OLD.id, OLD.employee_id, OLD.clientworktype_id, 
                OLD.wt_pay_std, OLD.wt_pay_ovr, OLD.wt_pay_ovr_midnight, OLD.wt_pay_holiday, OLD.wt_pay_holiday_midnight,
                OLD.wt_bill_std, OLD.wt_bill_ovr, OLD.wt_bill_ovr_midnight, OLD.wt_bill_holiday, OLD.wt_bill_holiday_midnight,
                OLD.notes);
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_delete');
    }
};