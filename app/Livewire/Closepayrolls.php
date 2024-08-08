<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;
use App\Models\closepayrolls as modelClosePayrolls;
use App\Models\employees as modelEmployees;
use App\Models\employeepays as modelEmployeePays;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeesalarys as modelEmployeeSalarys;
use App\Models\bills as modelBills;
use App\Models\billdetails as modelBillDetails;
use App\Models\pricetables as modelPriceTables;

class Closepayrolls extends Component
{
    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * closing is enabled or not
     * true if closing is enabled otherwise reopen is enabled
     */
    public $isClose = true;

    /**
     * enable close button
     * true if enable close button
     */
    public $enableCloseButton = true;

    /**
     * rules for validation
     */
    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

    /**
     * set isClose
     */
    protected function setIsClose()
    {
        // closepayrolls テーブルに対象年月のレコードが存在するか確認
        $closepayrolls = modelClosePayrolls::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        // 存在する場合は、closepayrolls テーブルの closed カラムの値を取得
        // closed カラムの値が true の場合は、isClose に false を設定
        $this->isClose = $closepayrolls ? ($closepayrolls->closed ? false : true) : true;        
    }

    /**
     * 従業員給与レコードを削除
     */
    protected function deleteEmployeeSalary()
    {
        // work_year, work_month に該当する従業員給与を削除
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        modelEmployeeSalarys::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->delete();
    }

    /**
     * 請求と請求明細レコードを削除
     */
    protected function deleteBill()
    {
        // work_year, work_month に該当する請求と請求明細を削除
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $Bills = modelBills::whereBetween('bill_date', [$sStartDay, $sEndDay])
            ->get();
        foreach($Bills as $Bill) {
            modelBillDetails::where('bill_id', $Bill->id)
                ->delete();
            $Bill->delete();
        }
    }

    /**
     * 従業員勤怠から従業員給与を計算
     */
    protected function calculatePayroll()
    {
        // work_year, work_month に該当する従業員勤怠を取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $Works = modelEmployeeWorks::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('employee_id, wrk_date, wrk_work_start')
            ->get();

        $saveEmployeeId = null;
        $saveWrkDate = null;
        $saveCliendId = null;
        $saveClientPlaceId = null;
        $saveWtCd = null;
        $isHoliday = false;
        foreach($Works as $Work) {
            // 従業員が変わった場合
            if ($saveEmployeeId != $Work->employee_id) {
                $Employee = modelEmployees::find($Work->employee_id);
                $saveEmployeeId = $Work->employee_id;
                $saveWrkDate = $Work->wrk_date;
                $saveCliendId = $Work->client_id;
                $saveClientPlaceId = $Work->clientplace_id;
                $saveWtCd = $Work->wt_cd;
                $payhour = modelEmployeePays::getPayhour($Work->employee_id, $Work->client_id, $Work->clientplace_id, $Work->wt_cd);
                $isHoliday = false; // Todo: 休日フラグを設定
                $wrk_ttl_seq = 1;
            }
            // 日付が変わった場合
            if ($saveWrkDate != $Work->wrk_date) {
                $saveWrkDate = $Work->wrk_date;
                $isHoliday = false; // Todo: 休日フラグを設定
                $wrk_ttl_seq = 1;
            }
            // 顧客または事業所が変わった場合
            if ($saveEmployeeId != $Work->employee_id) {
                $saveCliendId = $Work->client_id;
                $saveClientPlaceId = $Work->clientplace_id;
                $saveWtCd = $Work->wt_cd;
                $payhour = EmployeePays::getPayhour($Work->employee_id, $Work->client_id, $Work->clientplace_id, $Work->wt_cd);
            }
            // 金額を計算する
            try {
                $EmployeeSalary = new modelEmployeeSalarys();
                $EmployeeSalary->employee_id = $Work->employee_id;
                $EmployeeSalary->wrk_date = $Work->wrk_date;
                $EmployeeSalary->wrk_ttl_seq = $wrk_ttl_seq++;
                $EmployeeSalary->leave = $Work->leave ? $Work->leave : 0;
                $EmployeeSalary->client_id = $Work->client_id;
                $EmployeeSalary->clientplace_id = $Work->clientplace_id;
                $EmployeeSalary->wt_cd = $Work->wt_cd;
                // $EmployeeSalary->wrk_work_start = $Work->wrk_work_start;
                // $EmployeeSalary->wrk_work_end = $Work->wrk_work_end;
                $EmployeeSalary->wrk_work_start = $Work->wrk_log_start;
                $EmployeeSalary->wrk_work_end = $Work->wrk_log_end;
                // $dtStart = new DateTime($Work->wrk_work_start);
                // $dtEnd = new DateTime($Work->wrk_work_end);
                $dtStart = new DateTime($Work->wrk_log_start); // ToDo: 丸めた時間を使用すること
                $dtEnd = new DateTime($Work->wrk_log_end);
                $interval = $dtStart->diff($dtEnd);
                $EmployeeSalary->wrk_work_hours = sprintf('%d:%02d', $interval->h, $interval->i);
                $EmployeeSalary->payhour = $interval->h + ($interval->i / 60);
                $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_pay = floor($payhour * $EmployeeSalary->premium * $EmployeeSalary->payhour);
                $EmployeeSalary->save();
            }
            catch (\Exception $e) {
                // 例外処理 
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * 従業員勤怠から請求と請求明細を計算
     */
    protected function calculateBill()
    {
        // work_year, work_month に該当する従業員勤怠を取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $Works = modelEmployeeWorks::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('client_id, clientplace_id, wt_cd')
            ->get();

        $Bill = null;
        $BillDetail = null;
        $saveClientId = null;
        $saveClientPlaceId = null;
        $saveWtCd = null;
        $displayOrder = 1;
        foreach($Works as $Work) {
            // 顧客または事業所が変わった場合
            if ($saveClientId != $Work->client_id || $saveClientPlaceId != $Work->clientplace_id) {
                if($BillDetail)
                {
                    $BillDetail->save();
                    $BillDetail = null;
                }
                if($Bill)
                {
                    $Bill->save();
                    $Bill = null;
                }
                // 請求レコードを作成または再作成する
                $Bill = modelBills::createBill($Work->client_id, $Work->clientplace_id, $this->workYear, $this->workMonth);
                $BillDetail = null;
                $saveCliendId = $Work->client_id;
                $saveClientPlaceId = $Work->clientplace_id;
            }
            // 業務種別が変わった場合
            if ($saveWtCd != $Work->wt_cd) {
                // 請求単価を取得
                $PriceTable = modelPriceTables::where('client_id', $Work->client_id)
                    ->where('clientplace_id', $Work->clientplace_id)
                    ->where('wt_cd', $Work->wt_cd)
                    ->first();
                $unitPrice = $PriceTable ? $PriceTable->bill_unitprice : 0;
                if($BillDetail)
                {
                    $BillDetail->save();
                }
                $BillDetail = new modelBillDetails();
                $BillDetail->bill_id = $Bill->id;
                $BillDetail->display_order = $displayOrder++;
                $BillDetail->title = '明細';
                $BillDetail->unit_price = $unitPrice;
                $BillDetail->quantity = 0;
                $BillDetail->unit = '時間';
                $BillDetail->amount = 0;
                $BillDetail->tax = 0;
                $BillDetail->total = 0;
                $BillDetail->notes = '';
            }
            $dtStart = new DateTime('00:00:00');
            $dtEnd = new DateTime($Work->wrk_work_hours);
            $Interval = $dtEnd->diff($dtStart);
            $BillDetail->quantity += $Interval->h + floor($Interval->i / 60);
            $BillDetail->amount += $BillDetail->quantity * $unitPrice;
            $BillDetail->tax = floor($BillDetail->amount * 0.1);
            $BillDetail->total = $BillDetail->amount + $BillDetail->tax;
        }
        // 請求と請求明細を更新する　
        if($BillDetail)
        {
            $BillDetail->save();
            $BillDetail = null;
        }
        if($Bill)
        {
            $Bill->save();
            $Bill = null;
        }
    }

    /**
     * mount function
     */
    public function mount()
    {
        // 対象年月の初期設定
        // 日にちが < 15 の場合は、前月の年月を設定
        $this->workYear = date('Y');
        $this->workMonth = date('m');
        $Day = date('d');
        if ($Day < 15) {
            $this->workYear = date('Y', strtotime('-1 month'));
            $this->workMonth = date('m', strtotime('-1 month'));
        }

        // close or open の設定
        $this->setIsClose();
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.closepayrolls');
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->enableCloseButton = false;
        $this->validate();
        $this->setIsClose();
        $this->enableCloseButton = true;
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->enableCloseButton = false;
        $this->validate();
        $this->setIsClose();
        $this->enableCloseButton = true;
    }

    /**
     * close button click event
     * 
     * 従業員の勤怠をチェック
     * 勤怠を元に支給額を計算
     * 勤怠を元に請求額を計算
     * 勤怠締めレコードを作成または更新
     */
    public function closePayroll()
    {
        // ToDo: ここで従業員の勤怠をチェック
        // 従業員給与レコードを削除
        $this->deleteEmployeeSalary();
        // 従業員勤怠から従業員給与を計算
        $this->calculatePayroll();
        // 請求と請求明細レコードを削除
        $this->deleteBill();
        // 従業員勤怠から請求と請求明細を計算
        $this->calculateBill();
        // 勤怠締めレコードを更新する
        $ClosePayroll = new modelClosePayrolls();
        $ClosePayroll->updateOrCreate([
            'work_year' => $this->workYear, 
            'work_month' => $this->workMonth,
            'closed' => true, 
            'operation_date' => date('Y-m-d H:i:s')
        ]);
        Session::flash('message', '締め処理が完了しました。');
    }

    /**
     * reopen button click event
     */
    public function openPayroll()
    {
        // 従業員給与レコードを削除
        $this->deleteEmployeeSalary();
        // 請求と請求明細レコードを削除
        $this->deleteBill();
        // 勤怠締めレコードを更新する
        $ClosePayroll = new modelClosePayrolls();
        $ClosePayroll->updateOrCreate([
            'work_year' => $this->workYear, 
            'work_month' => $this->workMonth,
            'closed' => false, 
            'operation_date' => date('Y-m-d H:i:s')
        ]);
        Session::flash('message', '解除処理が完了しました。');
    }
}
