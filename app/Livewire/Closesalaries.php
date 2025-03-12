<?php

namespace App\Livewire;

use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\applogs;
use App\Models\salary as modelSalary;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeesalarys as modelEmployeeSalary;

use App\Services\PhpSpreadsheetService;

class Closesalaries extends Component
{
    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * rules for validation
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
            $Query = modelEmployeeWorks::with('employee')
                // ->select('employeeworks.*', 'employees.*')
                ->select ('employeeworks.employee_id', 'employees.empl_cd')
                ->join('employees', 'employeeworks.employee_id', '=', 'employees.id')
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
            $salary = modelSalary::where('employee_id', $employee_id)
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
     * mount function
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

    /**
     * render function
     */
    public function render()
    {

        // 各顧客、部門について、work_year, work_month に該当する従業員給与レコードを取得
        $Employees = $this->createEmployeeList();

        return view('livewire.closesalaries', compact('Employees'));
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->validate();
        session([AppConsts::WORK_YEAR => $this->workYear]);
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->validate();
        session([AppConsts::WORK_MONTH => $this->workMonth]);
    }

    /**
     * 給与出力
     */
    public function downloaSalaries()
    {
        $service = new PhpSpreadsheetService();
        session()->flash('success', '給与データを作成します。');

        // work_year, work_month に該当する給与レコードを取得
        $Salaries = modelSalary::with('employee')
            ->join('employees as employee', 'salarys.employee_id', '=', 'employee.id')
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->orderBy('employee.empl_cd')
            ->get();

        return $service->exportSalaries(
            storage_path('data/salary_template.xlsx'),
            [
                'work_year' => $this->workYear,
                'work_month' => $this->workMonth,
            ],
            $Salaries
        );
    }

    /**
     * 給与明細出力
     */
    public function downloadSalaryDetails()
    {
        $service = new PhpSpreadsheetService();
        session()->flash('success', '給与明細データを作成します。');

        // work_year, work_month に該当する給与レコードを取得
        $Salaries = modelSalary::with('employee')
            ->join('employees as employee', 'salarys.employee_id', '=', 'employee.id')
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->orderBy('employee.empl_cd')
            ->get();

        // work_year, work_month に該当する給与明細レコードを取得
        $workYear = $this->workYear;
        $workMonth = $this->workMonth;
        $sStartDay = $workYear . '-' . $workMonth . '-01';
        $sEndDay = $workYear . '-' . $workMonth . '-' . date('t', strtotime($sStartDay));
        $SalaryDetails = modelEmployeeSalary::with('employee')
        ->join('employees as employee', 'employeesalary.employee_id', '=', 'employee.id')
        ->whereBetween('wrk_date', [$sStartDay, $sEndDay])
        ->orderBy('employee.empl_cd')
        ->orderByRaw('wrk_date, wrk_work_start')
        ->get();

        return $service->exportSalaryDetails(
            storage_path('data/salary_template.xlsx'),
            [
                'work_year' => $this->workYear,
                'work_month' => $this->workMonth,
            ],
            $Salaries,
            $SalaryDetails
        );
    }
}
