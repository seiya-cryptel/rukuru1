<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\salary as modelSalary;
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
     * mount function
     */
    public function mount()
    {
        // 対象年月を設定
        // セッション変数にキー（workYear、workMonth）が設定されている場合は、その値を取得
        // 値を取得したあとは、セッション変数を削除
        if (session()->has('workYear')) {
            $this->workYear = session('workYear');
        } else {
            $this->workYear = date('Y');
        }
        if(session()->has('workMonth')) {
            $this->workMonth = session('workMonth');
        } else {
            $this->workMonth = date('m');
            $Day = date('d');
            if ($Day < 15) {
                $this->workYear = date('Y', strtotime('-1 month'));
                $this->workMonth = date('m', strtotime('-1 month'));
            }
        }
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.closesalaries');
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->validate();
        session(['workYear' => $this->workYear]);
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->validate();
        session(['workMonth' => $this->workMonth]);
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
