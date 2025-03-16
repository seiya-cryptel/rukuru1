<?php

namespace App\Livewire;

use Livewire\WithPagination;

use Livewire\Component;

use App\Consts\AppConsts;
use App\Services\PhpSpreadsheetService;

use App\Models\applogs as applogs;
use App\Models\employeeworks as employeeworks;
use App\Models\salary as salary;
use App\Models\clients as clients;
use App\Models\employees as employees;

/**
 * 有給日当計算クラス
 * 
 * EmployeeWorks 従業員勤怠と過去の Salary 給与レコードから有給日当を計算する
 * 
 */
class Pl extends Component
{
    use WithPagination;

    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * 有給日当計算結果
     */
    public $Salaries = [];

    /**
     * rules
     */
    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

    /**
     * 対象従業員一覧作成
     */
    protected function createEmployeeList()
    {
        // 対象年月に勤怠がある従業員
        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-01');
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        try{
            $Query = employeeworks::with('employee')
                ->join('employees', 'employeeworks.employee_id', '=', 'employees.id')
                ->select ('employeeworks.employee_id', 'employees.empl_cd')
                ->whereBetween('employeeworks.wrk_date', [date('Y-m-d', $dtFirstDate), date('Y-m-d', $dtLastDate)])
                ->orderByRaw('employees.empl_cd')
                ->distinct();
            $EmployeeWorks = $Query->get();
        } catch (\Exception $e) {
            $EmployeeWorks = [];
        }

        $Employees = [];
        foreach($EmployeeWorks as $EmployeeWork) {
            $employee_id = $EmployeeWork->employee_id;
            $salary = salary::where('employee_id', $employee_id)
                ->where('work_year', $this->workYear)
                ->where('work_month', $this->workMonth)
                ->first();
            $Employees[$employee_id] = [
                'empl_cd' => $EmployeeWork->employee->empl_cd,
                'empl_name' => $EmployeeWork->employee->empl_name_last . ' ' . $EmployeeWork->employee->empl_name_first,
                'work_amount' => $salary ? $salary->work_amount : 0,
                'transport' => $salary ? $salary->transport : 0,
                'allow_amount' => $salary ? $salary->allow_amount : 0,
                'deduct_amount' => $salary ? $salary->deduct_amount : 0,
                'pay_amount' => $salary ? $salary->pay_amount : 0,
            ];
        }   

        return $Employees;
    }

    /**
     * 有休日当を保存する
     */
    protected function savePayLeave($employee_id, $amount)
    {
        $Salary = salary::where('employee_id', $employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        if($Salary) {
            $Salary->paid_leave_pay = $amount;
            $Salary->save();
        }
        else{
            $Salary = new salary();
            $Salary->employee_id = $employee_id;
            $Salary->work_year = $this->workYear;
            $Salary->work_month = $this->workMonth;
            $Salary->pay_leave = $amount;
            $Salary->save();
        }
    }

    /**
     * 従業員の有給日当を計算する
     */
    public function calcPayLeaveByEmployee($employee_id, $Employee)
    {
        // 対象年月の初日
        $dtTargetDate = strtotime($this->workYear . '-' . $this->workMonth . '-01');

        $Salaries = [
            'employee_id' => $employee_id,
            'employee_cd' => $Employee['empl_cd'],
            'employee_name' => $Employee['empl_name'],
        ];
        // 過去3ヶ月の給与データを取得
        $PaidLeave = [];
        $pay_amount_total = 0;
        $working_days_total = 0;
        $days_total = 0;
        $indexMonth = 0;
        for($nMonth = -3; $nMonth < 0; $nMonth++)
        {
            $dtFirstDate = strtotime($nMonth . ' month', $dtTargetDate);
            $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));
            $workYear = date('Y', $dtFirstDate);
            $workMonth = date('m', $dtFirstDate);
            $dt = new \DateTime($workYear . '-' . $workMonth . '-01');

            $PaidLeave[$indexMonth] = [
                'employee_id' => $employee_id,
                'work_year' => $workYear,
                'work_month' => $workMonth,
                'days' => $dt->format('t'),
            ];
            $days_total += $dt->format('t');
            // 給与データ取得
            $Salary = salary::where('employee_id', $employee_id)
                ->where('work_year', $workYear)
                ->where('work_month', $workMonth)
                ->first();
            if($Salary) {
                $PaidLeave[$indexMonth]['pay_amount'] = $Salary->pay_amount;
                $PaidLeave[$indexMonth]['working_days'] = $Salary->working_days;
                $pay_amount_total += $Salary->pay_amount;
                $working_days_total += $Salary->working_days;
            }
            else {
                $PaidLeave[$indexMonth]['pay_amount'] = 0;
                $PaidLeave[$indexMonth]['working_days'] = 0;
            }
            $indexMonth++;
        }
        $paidLeavePayByWorkDay = $working_days_total ? round($pay_amount_total / $working_days_total * 0.6, 0) : 0;
        $paidLeavePayByDay = $days_total ? round($pay_amount_total / $days_total, 0) : 0;
        $Salaries['payLeave'] = $PaidLeave;
        $Salaries['pay_amount_total'] = $pay_amount_total;
        $Salaries['working_days_total'] = $working_days_total;
        $Salaries['days_total'] = $days_total;
        $Salaries['paid_leave_pay'] = max($paidLeavePayByWorkDay, $paidLeavePayByDay);
        $this->Salaries[$employee_id] = $Salaries;
        // 有休日当を設定
        $this->savePayLeave($employee_id, $Salaries['paid_leave_pay']);
}

    /**
     * mount 
     */
    public function mount()
    {
        // 対象年月を設定
        // セッション変数にキー（workYear、workMonth）が設定されている場合は、その値を取得
        if (session()->has(AppConsts::SESS_WORK_YEAR)) {
            $this->workYear = session(AppConsts::SESS_WORK_YEAR);
        } else {
            $this->workYear = date('Y');
            session([AppConsts::SESS_WORK_YEAR => $this->workYear]);
        }
        if(session()->has(AppConsts::SESS_WORK_MONTH)) {
            $this->workMonth = session(AppConsts::SESS_WORK_MONTH);
        } else {
            $this->workMonth = date('m');
            $Day = date('d');
            if ($Day < 15) {
                $this->workYear = date('Y', strtotime('-1 month'));
                $this->workMonth = date('m', strtotime('-1 month'));
            }
            session([AppConsts::SESS_WORK_MONTH => $this->workMonth]);
        }
    }

    public function render()
    {
        $Employees = $this->createEmployeeList();

        return view('livewire.pl', compact('Employees'));
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->validate();
        session([AppConsts::SESS_WORK_YEAR => $this->workYear]);
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->validate();
        session([AppConsts::SESS_WORK_MONTH => $this->workMonth]);
    }

    /**
     * 有休日当を計算する
     */
    public function calcPayLeave()
    {
        // 対象者
        $Employees = $this->createEmployeeList();

        try {
            // 従業員ごとに
            foreach($Employees as $employee_id => $Employee) {
                $this->calcPayLeaveByEmployee($employee_id, $Employee);
            }

            $logMessage = '有給日当計算: ' . $this->workYear . '年' . $this->workMonth . '月';
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_PAID_LEAVE, $logMessage);
            session()->flash('success', '有給日当計算が完了しました。');
        } catch (\Exception $e) {
            $logMessage = '有給日当計算エラー: ' . $this->workYear . '年' . $this->workMonth . '月'
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_PAID_LEAVE, $logMessage);
            session()->flash('error', '有給日当計算に失敗しました。');
        }
    }
}
