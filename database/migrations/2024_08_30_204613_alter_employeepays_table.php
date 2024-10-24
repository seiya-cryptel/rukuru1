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
        Schema::table('employeepays', function (Blueprint $table) {
            $table->unsignedBigInteger('clientworktype_id')->after('employee_id')->comment('顧客業務種別ID');
            $table->dropColumn('client_id');
            $table->dropColumn('clientplace_id');
            $table->dropColumn('wt_cd');
            $table->dropColumn('payhour');
            $table->dropColumn('billhour');
            $table->decimal('wt_pay_std', 12, 4)->nullable()->comment('標準時給')->after('clientworktype_id');
            $table->decimal('wt_pay_ovr', 12, 4)->nullable()->comment('残業')->after('wt_pay_std');
            $table->decimal('wt_pay_ovr_midnight', 12, 4)->nullable()->comment('深夜')->after('wt_pay_ovr');
            $table->decimal('wt_pay_holiday', 12, 4)->nullable()->comment('法定休日')->after('wt_pay_ovr_midnight');
            $table->decimal('wt_pay_holiday_midnight', 12, 4)->nullable()->comment('法定休日深夜')->after('wt_pay_holiday');
            $table->decimal('wt_bill_std', 12, 4)->nullable()->comment('標準請求')->after('wt_pay_holiday_midnight');
            $table->decimal('wt_bill_ovr', 12, 4)->nullable()->comment('残業')->after('wt_bill_std');
            $table->decimal('wt_bill_ovr_midnight', 12, 4)->nullable()->comment('深夜')->after('wt_bill_ovr');
            $table->decimal('wt_bill_holiday', 12, 4)->nullable()->comment('法定休日')->after('wt_bill_ovr_midnight');
            $table->decimal('wt_bill_holiday_midnight', 12, 4)->nullable()->comment('法定休日深夜')->after('wt_bill_holiday');

            $table->unique(['employee_id', 'clientworktype_id']);
        });

        Schema::table('xlog_employeepays', function (Blueprint $table) {
            $table->unsignedBigInteger('clientworktype_id')->after('employee_id')->comment('顧客業務種別ID');
            $table->dropColumn('client_id');
            $table->dropColumn('clientplace_id');
            $table->dropColumn('wt_cd');
            $table->dropColumn('payhour');
            $table->dropColumn('billhour');
            $table->decimal('wt_pay_std', 12, 4)->nullable()->after('clientworktype_id');
            $table->decimal('wt_pay_ovr', 12, 4)->nullable()->after('wt_pay_std');
            $table->decimal('wt_pay_ovr_midnight', 12, 4)->nullable()->after('wt_pay_ovr');
            $table->decimal('wt_pay_holiday', 12, 4)->nullable()->after('wt_pay_ovr_midnight');
            $table->decimal('wt_pay_holiday_midnight', 12, 4)->nullable()->after('wt_pay_holiday');
            $table->decimal('wt_bill_std', 12, 4)->nullable()->after('wt_pay_holiday_midnight');
            $table->decimal('wt_bill_ovr', 12, 4)->nullable()->after('wt_bill_std');
            $table->decimal('wt_bill_ovr_midnight', 12, 4)->nullable()->after('wt_bill_ovr');
            $table->decimal('wt_bill_holiday', 12, 4)->nullable()->after('wt_bill_ovr_midnight');
            $table->decimal('wt_bill_holiday_midnight', 12, 4)->nullable()->after('wt_bill_holiday');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_delete');

        DB::unprepared('CREATE TRIGGER trg_employeepays_update AFTER UPDATE ON `employeepays` FOR EACH ROW
        BEGIN
            INSERT INTO `xlog_employeepays`
                (`opr`, `employeepays_id`, `employee_id`, `clientworktype_id`,
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
                (`opr`, `employeepays_id`, `employee_id`, `clientworktype_id`,
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
        Schema::table('employeepays', function (Blueprint $table) {
            $table->dropColumn('clientworktype_id');
            $table->unsignedBigInteger('client_id')->after('employee_id');
            $table->unsignedBigInteger('clientplace_id')->after('client_id');
            $table->string('wt_cd', 8)->after('clientplace_id');
            $table->decimal('payhour', 12, 4)->after('wt_cd');
            $table->decimal('billhour', 12, 4)->after('payhour');
            $table->dropColumn('wt_pay_std');
            $table->dropColumn('wt_pay_ovr');
            $table->dropColumn('wt_pay_ovr_midnight');
            $table->dropColumn('wt_pay_holiday');
            $table->dropColumn('wt_pay_holiday_midnight');
            $table->dropColumn('wt_bill_std');
            $table->dropColumn('wt_bill_ovr');
            $table->dropColumn('wt_bill_ovr_midnight');
            $table->dropColumn('wt_bill_holiday');
            $table->dropColumn('wt_bill_holiday_midnight');
        });

        Schema::table('xlog_employeepays', function (Blueprint $table) {
            $table->dropColumn('clientworktype_id');
            $table->unsignedBigInteger('client_id')->after('employee_id');
            $table->unsignedBigInteger('clientplace_id')->after('client_id');
            $table->string('wt_cd', 8)->after('clientplace_id');
            $table->decimal('payhour', 12, 4)->after('wt_cd');
            $table->decimal('billhour', 12, 4)->after('payhour');
            $table->dropColumn('wt_pay_std');
            $table->dropColumn('wt_pay_ovr');
            $table->dropColumn('wt_pay_ovr_midnight');
            $table->dropColumn('wt_pay_holiday');
            $table->dropColumn('wt_pay_holiday_midnight');
            $table->dropColumn('wt_bill_std');
            $table->dropColumn('wt_bill_ovr');
            $table->dropColumn('wt_bill_ovr_midnight');
            $table->dropColumn('wt_bill_holiday');
            $table->dropColumn('wt_bill_holiday_midnight');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employeepays_delete');
    }
};
