<?php

namespace App\Livewire;

use Livewire\Component;

use App\Traits\rukuruUtilities;

use App\Models\employees as modelEmployees;
use App\Models\masterallowdeducts as modelMasterAllowDeducts;
use App\Models\employeesalarys as modelEmployeeSalarys;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;
use App\Models\salary as modelSalary;

class Employeesalarys extends Component
{
    use rukuruUtilities;

    #[Layout('layouts.app')]

    // parameters
    public $workYear;
    public $workMonth;
    public $employee_id;

    /**
     * Allow/Deduct reference
     */
    public $Employee;
    public $Salarys;
    public $refAllows;
    public $refDeducts;
    public $refEmployeeSalarys;
    public $refClientWorkTypes;

    /**
     * allow/deduct array
     */
    public $Allows = [];
    public $Deducts = [];
    public $Transport = 0;
    
    /**
     * 合計額
     */
    public $TotalAllow = 0;     // 手当合計
    public $TotalDeduct = 0;    // 控除合計
    public $TotalPay = 0;       // 給与合計
    public $PayAmount = 0;      // 支給額

    /**
     * 支給額 給与計 + 交通費 + 手当計 - 控除計 を計算する
     */
    protected function getPayAmount()
    {
        $this->TotalAllow = 0;
        $this->TotalDeduct = 0;
        for($i = 0; $i < 10; $i++)
        {
            $amount = $this->rukuruUtilMoneyValue($this->Allows[$i]['amount']);
            $this->TotalAllow += $amount ? $amount : 0;
            $amount = $this->rukuruUtilMoneyValue($this->Deducts[$i]['amount']);
            $this->TotalDeduct += $amount ? $amount : 0;
        }
        $transport = $this->rukuruUtilMoneyValue($this->Transport);
        $totalPay = $this->rukuruUtilMoneyValue($this->TotalPay);
        $this->PayAmount = $totalPay + $transport + $this->TotalAllow - $this->TotalDeduct;
        // 数値編集
        $this->TotalAllow = number_format($this->TotalAllow);
        $this->TotalDeduct = number_format($this->TotalDeduct);
        $this->PayAmount = number_format($this->PayAmount);
    }

    /**
     * mount function
     */
    public function mount($workYear, $workMonth, $employee_id)
    {
        $this->employee_id = $employee_id;
        $this->workYear = $workYear;
        $this->workMonth = $workMonth;
        if(!$this->employee_id || !$this->workYear || !$this->workMonth) {
            return redirect()->route('salaryemployee');
        }

        // 従業員情報を取得
        $this->Employee = modelEmployees::find($this->employee_id);

        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        // 支給詳細情報を取得
        $this->refEmployeeSalarys = modelEmployeeSalarys::where('employee_id', $employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->orderBy('wrk_date')
            ->orderBy('wrk_work_start')
            ->get();
        // 支給合計額を計算
        $this->TotalPay = modelEmployeeSalarys::where('employee_id', $employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->sum('wrk_pay');
        $this->TotalPay = number_format($this->TotalPay);
        // 現状の支給額を設定
        $this->PayAmount = preg_replace('/[^0-9.]/', '', $this->TotalPay);
        // 交通費を加算
        $this->PayAmount += preg_replace('/[^0-9.]/', '', $this->Transport);

        $this->refAllows = modelMasterAllowDeducts::where('mad_allow', '1')->get();
        $this->refDeducts = modelMasterAllowDeducts::where('mad_deduct', '1')->get();

        for($i = 0; $i < 10; $i++)
        {
            $this->Allows[$i] = [
                'id' => null,
                'amount' => 0,
            ];
            $this->Deducts[$i] = [
                'id' => null,
                'amount' => 0,
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
                'id' => $Allow->masterallowdeduct_id,
                'amount' => number_format($Allow->amount),
            ];
        }
        $i = 0;
        foreach($Deducts as $Deduct)
        {
            $this->Deducts[$i++] = [
                'id' => $Allow->masterallowdeduct_id,
                'amount' => number_format($Deduct->amount),
            ];
        }   
        $this->Salarys = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        $this->Transport = $this->Salarys ? number_format($this->Salarys->transport) : 0;

        // 数値編集して表示
        $this->getPayAmount();
    }

     /**
      * render function
      */
    public function render()
    {
        return view('livewire.employeesalarys');
    }

    /**
     * 手当、控除金額が変更されたときに呼び出される
     * @param string $money 金額入力
     * @param string $field
     * @param int $index
     * @return void
     * 交通費
     */
    public function moneyChange($money, $field, $index)
    {
        $money = preg_replace('/[^0-9.]/', '', $money);
        $this->$field[$index]['amount'] = empty($money) ? '' : number_format($money);
        $this->getPayAmount();
    }

    /**
     * 金額項目が変更されたときに呼び出される
     * @param string $money, string $field
     * @return void
     * 交通費
     */
    public function transportChange($money)
    {
        $money = preg_replace('/[^0-9.]/', '', $money);
        $this->Transport = empty($money) ? '' : number_format($money);
        $this->getPayAmount();
    }

    /**
     * save
     */
    public function saveEmployeeSalary()
    {
        // 手当を保存
        for($i = 0; $i < 10; $i++)
        {
            if ($this->Allows[$i]['id']) {
                $this->Allows[$i]['amount'] = $this->rukuruUtilMoneyValue($this->Allows[$i]['amount']);
            }
            if ($this->Deducts[$i]['id']) {
                $this->Deducts[$i]['amount'] = $this->rukuruUtilMoneyValue($this->Deducts[$i]['amount']);
            }
        }
        $this->Transport = $this->rukuruUtilMoneyValue($this->Transport);

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
            if ($this->Allows[$i]['id']) {
                $master = modelMasterAllowDeducts::find($this->Allows[$i]['id']);
                $mad = new modelEmployeeAllowDeduct();
                $mad->employee_id = $this->employee_id;
                $mad->work_year = $this->workYear;
                $mad->work_month = $this->workMonth;
                $mad->masterallowdeduct_id = $this->Allows[$i]['id'];
                $mad->mad_cd = $master->mad_cd;
                $mad->mad_deduct = 0;
                $mad->mad_name = $master->mad_name;
                $mad->amount = $this->Allows[$i]['amount'];
                $salary->allow_amount += $mad->amount;
                $mad->save();
            }
        }
        for($i = 0; $i < 10; $i++)
        {
            if ($this->Deducts[$i]['id']) {
                $master = modelMasterAllowDeducts::find($this->Deducts[$i]['id']);
                $mad = new modelEmployeeAllowDeduct();
                $mad->employee_id = $this->employee_id;
                $mad->work_year = $this->workYear;
                $mad->work_month = $this->workMonth;
                $mad->masterallowdeduct_id = $this->Allows[$i]['id'];
                $mad->mad_cd = $master->mad_cd;
                $mad->mad_deduct = 1;
                $mad->mad_name = $master->mad_name;
                $mad->amount = $this->Deducts[$i]['amount'];
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
