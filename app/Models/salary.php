<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\employeesalarys as modelEmployeeSalarys;

class salary extends Model
{
    use HasFactory;

    protected $table = 'salarys';

    /**
     * Relationship with client
     */
    public function employee()
    {
        return $this->belongsTo(employees::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'work_year',
        'work_month',
        'work_amount',
        'allow_amount',
        'deduct_amount',
        'transport',
        'pay_amount',
        'notes',
    ];

    /**
     * 従業員ID、対象年月から給与情報を作成または再作成
     */
    public static function createSalary($employeeId, $workYear, $workMonth)
    {
        // 従業員の勤怠支給額
        $EmployeeSalary = modelEmployeeSalarys::where('employee_id', $employeeId)
            ->where('work_year', $workYear)
            ->where('work_month', $workMonth)
            ->selectRaw('sum(wrk_pay) as work_amount')
            ->first();
        $workAmount = $EmployeeSalary[0]->work_amount;

        // 従業員ID、対象年月に該当する給与情報を取得
        $salary = salary::where('employee_id', $employeeId)
            ->where('work_year', $workYear)
            ->where('work_month', $workMonth)
            ->first();

        // 給与情報が存在しない場合は新規作成
        if (empty($salary)) {
            $salary = new salary();
            $salary->employee_id = $employeeId;
            $salary->work_year = $workYear;
            $salary->work_month = $workMonth;
            $salary->work_amount = 0;
            $salary->allow_amount = 0;
            $salary->deduct_amount = 0;
            $salary->transport = 0;
            $salary->pay_amount = 0;
            $salary->notes = '';
            $salary->save();
        }

        return $salary;
    }
}
