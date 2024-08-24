<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\employees as modelEmployees;
use App\Models\masterallowdeducts as modelMasterAllowDeducts;
use App\Models\employeesalarys as modelEmployeeSalarys;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;
use App\Models\salary as modelSalary;

class Employeesalarys extends Component
{
    #[Layout('layouts.app')]

    // parameters
    public $workYear;
    public $workMonth;
    public $employee_id;

    /**
     * Allow/Deduct reference
     */
    public $Employee;
    public $refAllows;
    public $refDeducts;
    public $refEmployeeSalarys;

    /**
     * allow/deduct array
     */
    public $Allows = [];
    public $Deducts = [];
    public $Transport = 0;

    /**
     * mount function
     */
    public function mount()
    {
        // セッション変数から取得する
        $this->employee_id = session('employee_id');
        $this->workYear = session('workYear');
        $this->workMonth = session('workMonth');
        if(!$this->employee_id || !$this->workYear || !$this->workMonth) {
            return redirect()->route('salaryemployee');
        }
        // セッション変数を削除する
        session()->forget('employee_id');
        session()->forget('workYear');
        session()->forget('workMonth');

        $this->Employee = modelEmployees::find($this->employee_id);

        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        // $this->refEmployeeSalarys = modelEmployeeSalarys::where('employee_id', $this->employee_id)
        $this->refEmployeeSalarys = modelEmployeeSalarys::where('employee_id', 16)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->get();
        $this->refAllows = modelMasterAllowDeducts::where('mad_allow', '1')->get();
        $this->refDeducts = modelMasterAllowDeducts::where('mad_deduct', '1')->get();

        for($i = 0; $i < 10; $i++)
        {
            $this->Allows[$i] = [
                'mad_cd' => '',
                'mad_amount' => 0,
            ];
            $this->Deducts[$i] = [
                'mad_cd' => '',
                'mad_amount' => 0,
            ];
        }
        $Allows = modelEmployeeAllowDeduct::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->where('mad_deduct', '0')
            ->get();
        $Deducts = modelEmployeeAllowDeduct::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->where('mad_deduct', '1')
            ->get();
        $i = 0;
        foreach($Allows as $Allow)
        {
            $this->Allows[$i++] = [
                'mad_cd' => $Allow->mad_cd,
                'mad_amount' => $Allow->amount,
            ];
        }
        $i = 0;
        foreach($Deducts as $Deduct)
        {
            $this->Deducts[$i++] = [
                'mad_cd' => $Deduct->mad_cd,
                'mad_amount' => $Deduct->amount,
            ];
        }   
        $salary = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        $this->Transport = $salary->transport;
    }

     /**
      * render function
      */
    public function render()
    {
        return view('livewire.employeesalarys');
    }

    /**
     * save
     */
    public function saveEmployeeSalary()
    {
        // 手当を保存
        for($i = 0; $i < 10; $i++)
        {
            if ($this->Allows[$i]['mad_cd'] != '') {
                $this->Allows[$i]['mad_amount'] = str_replace(',', '', $this->Allows[$i]['mad_amount']);
            }
            if ($this->Deducts[$i]['mad_cd'] != '') {
                $this->Deducts[$i]['mad_amount'] = str_replace(',', '', $this->Deducts[$i]['mad_amount']);
            }
        }
        $this->Transport = str_replace(',', '', $this->Transport);

        // 従業員ID、対象年月から給与情報を作成または再作成
        $salary = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        if(!$salary) {
            $salary = new modelSalary();
            $salary->employee_id = $this->employee_id;
            $salary->work_year = $this->workYear;
            $salary->work_month = $this->workMonth;
        }

        // 給与情報を更新
        $salary->allow_amount = 0;
        $salary->deduct_amount = 0;
        $salary->transport = $this->Transport;
        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $salary->work_amount = modelEmployeeSalarys::where('employee_id', $this->employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->sum('wrk_pay');
        $salary->notes = '';

        // 給与情報の手当・控除を更新
        modelEmployeeAllowDeduct::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->delete();
        for($i = 0; $i < 10; $i++)
        {
            if ($this->Allows[$i]['mad_cd'] != '') {
                $mad = new modelEmployeeAllowDeduct();
                $mad->employee_id = $this->employee_id;
                $mad->work_year = $this->workYear;
                $mad->work_month = $this->workMonth;
                $mad->mad_cd = $this->Allows[$i]['mad_cd'];
                $mad->mad_deduct = 0;
                $mad->amount = $this->Allows[$i]['mad_amount'];
                $salary->allow_amount += $mad->amount;
                $mad->save();
            }
            if ($this->Deducts[$i]['mad_cd'] != '') {
                $mad = new modelEmployeeAllowDeduct();
                $mad->employee_id = $this->employee_id;
                $mad->work_year = $this->workYear;
                $mad->work_month = $this->workMonth;
                $mad->mad_cd = $this->Deducts[$i]['mad_cd'];
                $mad->mad_deduct = 1;
                $mad->amount = $this->Deducts[$i]['mad_amount'];
                $salary->deduct_amount += $mad->amount;
                $mad->save();
            }
        }

        // 給与情報の支給額を更新
        $salary->pay_amount = $salary->work_amount + $salary->allow_amount - $salary->deduct_amount;
        $salary->save();

        // redirect to workemployees
        return redirect()->route('salaryemployee');
    }

    /**
     * cancel
     */
    public function cancelEmployeeSalary()
    {
        // セッション変数にキーを設定する
        session(['workYear' => $this->workYear]);
        session(['workMonth' => $this->workMonth]);

        // redirect to workemployees
        return redirect()->route('salaryemployee');
    }
}
