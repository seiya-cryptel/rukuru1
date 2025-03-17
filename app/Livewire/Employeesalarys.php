<?php

namespace App\Livewire;

use Livewire\Component;

use App\Consts\AppConsts;

use App\Traits\rukuruUtilities;

use App\Models\employees as modelEmployees;
use App\Models\masterallowdeducts as modelMasterAllowDeducts;
use App\Models\employeesalarys as modelEmployeeSalarys;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;
use App\Models\salary as modelSalary;

/**
 * 手当控除入力クラス
 */
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
    
    /**
     * 項目数の最大
     */
    public $maxItems = AppConsts::MAX_ALLOW_DEDUCTS;

    /**
     * allow/deduct array
     */
    public $Allows = [];
    public $Deducts = [];
    public $Transport = 0;
    public $Transport_masterallowdeduct_id = 0;

    /**
     * 合計額
     */
    public $TotalAllow = 0;     // 手当合計
    public $TotalDeduct = 0;    // 控除合計
    public $TotalPay = 0;       // 給与合計
    public $PayAmount = 0;      // 支給額

    /**
     * 従業員選択用id
     */
    public $nextEmployeeId;

    /**
     * 勤怠入力に戻る
     */
    public $boolReturnToWorkEntry = false;

    /**
     * 支給額 勤怠計 + 交通費 + 手当計 - 控除計 を計算する
     */
    protected function getPayAmount()
    {
        $this->TotalAllow = 0;
        $this->TotalDeduct = 0;
        for($i = 0; $i < $this->maxItems; $i++)
        {
            $amount = $this->rukuruUtilMoneyValue($this->Allows[$i]['amount']);
            $this->TotalAllow += $amount ? $amount : 0;
            $amount = $this->rukuruUtilMoneyValue($this->Deducts[$i]['amount']);
            $this->TotalDeduct += $amount ? $amount : 0;
        }
        $transport = $this->rukuruUtilMoneyValue($this->Transport);
        $this->totalPay = $this->rukuruUtilMoneyValue($this->TotalPay);
        $this->PayAmount = $this->totalPay + $transport + $this->TotalAllow - $this->TotalDeduct;
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
        $this->nextEmployeeId = $employee_id;
        if(!$this->employee_id || !$this->workYear || !$this->workMonth) {
            session()->flash('error', __('Employee') . ' ' . __('Not Found'));
            return redirect()->route('salaryemployee');
        }

        // 戻り先を設定
        $this->boolReturnToWorkEntry = 
            (session()->has(AppConsts::SESS_PREVIOUS_URL) && (session(AppConsts::SESS_PREVIOUS_URL) == 'employeework')) ? true : false;

        // 従業員情報を取得
        $this->Employee = modelEmployees::find($this->employee_id);
        if(!$this->Employee) {
            session()->flash('error', __('Employee') . ' ' . __('Not Found'));
            return redirect()->route('salaryemployee');
        }
        // 給与レコード
        $this->Salarys = modelSalary::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();

        // 勤怠の対象期間を設定
        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));

        // 支給詳細情報を取得
        $this->refEmployeeSalarys = modelEmployeeSalarys::where('employee_id', $employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->orderBy('wrk_date')
            ->orderBy('wrk_work_start')
            ->get();

        // 勤怠支給合計額を計算
        $this->TotalPay = modelEmployeeWorks::where('employee_id', $employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->sum('wrk_pay');
        $this->TotalPay = number_format($this->TotalPay);

        // 現状の支給額を設定
        $this->PayAmount = $this->rukuruUtilMoneyValue($this->TotalPay);

        // 交通費を加算
        $this->PayAmount += $this->rukuruUtilMoneyValue($this->Transport);

        $AllowTransport = modelMasterAllowDeducts::where('mad_allow', '1')
            ->where('mad_cd', '=', AppConsts::MAD_CD_TRANSPORT)   // 交通費
            ->first();
        $this->refAllows = modelMasterAllowDeducts::where('mad_allow', '1')
            ->where('mad_cd', '<>', AppConsts::MAD_CD_TRANSPORT)   // 交通費を除く
            ->orderBy('mad_cd')
            ->get();
        $this->refDeducts = modelMasterAllowDeducts::where('mad_deduct', '1')
            ->orderBy('mad_cd')
            ->get();

        $this->Transport_masterallowdeduct_id = $AllowTransport->id;
        $this->Transport = 0;
        for($i = 0; $i < $this->maxItems; $i++)
        {
            $this->Allows[$i] = [
                'id' => null,
                'amount' => '',
                'readonly' => 'readonly="readonly"',
            ];
            $this->Deducts[$i] = [
                'id' => null,
                'amount' => '',
                'readonly' => 'readonly="readonly"',
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
            if($Allow->mad_cd == AppConsts::MAD_CD_TRANSPORT) {
                $this->Transport = number_format($Allow->amount);
                continue;
            }
            $this->Allows[$i++] = [
                'id' => $Allow->masterallowdeduct_id,
                'amount' => number_format($Allow->amount),
                'readonly' => ($Allow->masterallowdeduct_id) ? '' : 'readonly="readonly"',
            ];
        }
        $i = 0;
        foreach($Deducts as $Deduct)
        {
            $this->Deducts[$i++] = [
                'id' => $Deduct->masterallowdeduct_id,
                'amount' => number_format($Deduct->amount),
                'readonly' => ($Deduct->masterallowdeduct_id) ? '' : 'readonly="readonly"',
            ];
        }   

        // 数値編集して表示
        $this->getPayAmount();
    }

     /**
      * render function
      */
    public function render()
    {
        $Employees = modelEmployees::orderBy('empl_cd')
            ->get();

        return view('livewire.employeesalarys', compact('Employees'));
    }

    /**
     * 従業員が変更された
     */
    public function employeeChanged($nextEmployeeId)
    {
        // 手当控除を保存する
        $this->saveEmployeeSalary();
        // 新しい手当控除入力画面に移動する
        return redirect()->route('employeesalary', [
            'workYear' => $this->workYear, 
            'workMonth' => $this->workMonth, 
            'employeeId' => $nextEmployeeId,
        ]);
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
        $money = $this->rukuruUtilMoneyValue($money, 0);
        $this->$field[$index]['amount'] = $money=='' ? '' : number_format($money);
        $this->getPayAmount();
    }

    /**
     * 交通費が変更されたときに呼び出される
     * @param string $money, string $field
     * @return void
     * 交通費
     */
    public function transportChange($money)
    {
        $money = $this->rukuruUtilMoneyValue($money, 0);
        $this->Transport = number_format($money);
        $this->getPayAmount();
    }

    /**
     * 手当項目が変更されたとき
     */
    public function allowChange($id, $index)
    {
        $this->Allows[$index]['id'] = $id;
        if(!$id) {
            $this->Allows[$index]['amount'] = 0;
        }
        $this->Allows[$index]['readonly'] = $id ? '' : 'readonly="readonly"';        
    }

    /**
     * 控除項目が変更されたとき
     */
    public function deductChange($id, $index)
    {
        $this->Deducts[$index]['id'] = $id;
        if(!$id) {
            $this->Deducts[$index]['amount'] = 0;
        }
        $this->Deducts[$index]['readonly'] = $id ? '' : 'readonly="readonly"';        
    }

    /**
     * save
     */
    public function saveEmployeeSalary()
    {
        // 手当・控除を数値化
        for($i = 0; $i < $this->maxItems; $i++)
        {
            if ($this->Allows[$i]['id']) {
                $this->Allows[$i]['amount'] = $this->rukuruUtilMoneyValue($this->Allows[$i]['amount']);
            }
            if ($this->Deducts[$i]['id']) {
                $this->Deducts[$i]['amount'] = $this->rukuruUtilMoneyValue($this->Deducts[$i]['amount']);
            }
        }
        // 交通費を数値化
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
            $salary->Transport = 0;
            $salary->allow_amount = 0;
            $salary->deduct_amount = 0;
        }

        // 給与情報を更新
        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $salary->work_amount = modelEmployeeSalarys::where('employee_id', $this->employee_id)
            ->whereBetween('wrk_date', [$firstDate, $lastDate])
            ->sum('wrk_pay');
        $salary->allow_amount = 0;
        $salary->deduct_amount = 0;
        $salary->notes = '';

        // 給与情報の手当・控除を更新
        modelEmployeeAllowDeduct::where('employee_id', $this->employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->delete();

        // 交通費を手当として登録
        $master = modelMasterAllowDeducts::find($this->Transport_masterallowdeduct_id);
        if(!$master) {
            session()->flash('error', 'プログラムエラー 手当控除マスタの交通費を検索できません。');
            return;
        }
        $mad = new modelEmployeeAllowDeduct();
        $mad->employee_id = $this->employee_id;
        $mad->work_year = $this->workYear;
        $mad->work_month = $this->workMonth;
        $mad->masterallowdeduct_id = $this->Transport_masterallowdeduct_id;
        $mad->mad_cd = AppConsts::MAD_CD_TRANSPORT;
        $mad->mad_deduct = 0;
        $mad->mad_name = $master->mad_name;
        $mad->amount = $this->Transport;
        $salary->Transport += $this->Transport;;
        $mad->save();

        // 手当登録
        for($i = 0; $i < $this->maxItems; $i++)
        {
            if ($this->Allows[$i]['id']) {
                $master = modelMasterAllowDeducts::find($this->Allows[$i]['id']);
                if(!$master) {
                    session()->flash('error', 'プログラムエラー 手当控除マスタの手当を検索できません。');
                    return;
                }
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
        // 控除登録
        for($i = 0; $i < $this->maxItems; $i++)
        {
            if ($this->Deducts[$i]['id']) {
                $master = modelMasterAllowDeducts::find($this->Deducts[$i]['id']);
                if(!$master) {
                    session()->flash('error', 'プログラムエラー 手当控除マスタの控除を検索できません。');
                    return;
                }
                $mad = new modelEmployeeAllowDeduct();
                $mad->employee_id = $this->employee_id;
                $mad->work_year = $this->workYear;
                $mad->work_month = $this->workMonth;
                $mad->masterallowdeduct_id = $this->Deducts[$i]['id'];
                $mad->mad_cd = $master->mad_cd;
                $mad->mad_deduct = 1;
                $mad->mad_name = $master->mad_name;
                $mad->amount = $this->Deducts[$i]['amount'];
                $salary->deduct_amount += $mad->amount;
                $mad->save();
            }
        }

        // 給与情報の支給額を更新
        $salary->pay_amount = $salary->work_amount + $salary->allow_amount - $salary->deduct_amount + $salary->Transport;
        $salary->save();

        // redirect to workemployees
        return redirect()->route('salaryemployee');
    }

    /**
     * cancel
     */
    public function cancelEmployeeSalary()
    {
        // 勤怠入力に戻る条件
        if($this->boolReturnToWorkEntry) {
            $client_id = session(AppConsts::SESS_CLIENT_ID);
            $clientplace_id = session(AppConsts::SESS_CLIENT_PLACE_ID);

            return redirect()->route('employeework', 
                ['workYear' => $this->workYear, 'workMonth' => $this->workMonth, 
                'clientId' => $client_id, 'clientPlaceId' => $clientplace_id, 'employeeId' => $this->employee_id]);
        }

        // redirect to workemployees
        return redirect()->route('salaryemployee');
    }
}
