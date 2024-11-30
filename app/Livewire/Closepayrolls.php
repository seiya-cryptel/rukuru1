<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Illuminate\Support\Facades\Session;

use Livewire\Component;

use App\Traits\rukuruUtil;

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
    use rukuruUtil;

    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * closing is enabled or not
     * true if closing is enabled otherwise reopen is enabled
     */
    public $isClosed = true;

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

	protected $saveEmployeeId = null; // 従業員ID
	protected $saveWrkDate = null;    // 勤務日
	protected $wk_ttl_seq = null;     // 1日の中の勤怠連番
	protected $saveCliendId = null;   // 顧客ID
	protected $saveClientPlaceId = null;  // 事業所ID
	protected $saveWtCd = null;   // 作業種別コード
	protected $cdHoliday = false; // 休日種別 rukuruUtilIsHoliday で設定
    // 計算に必要なレコード
	protected $curEmployee = null;
	protected $curClient = null;
	protected $curClientPlace = null;
    protected $curClientWorkType = null;

    /**
     * set isClosed
     */
    protected function setisClosed()
    {
        // closepayrolls テーブルに対象年月のレコードが存在するか確認
        $closepayrolls = modelClosePayrolls::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        // 存在する場合は、closepayrolls テーブルの closed カラムの値を取得
        // closed カラムの値が true の場合は、isClosed に false を設定
        $this->isClosed = $closepayrolls ? ($closepayrolls->closed ? true : false) : false;
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
        $Bills = modelBills::whereBetween('bill_date', [$sStartDay, $sEndDay])
            ->delete();
    }

    /**
     * 給与と請求の計算：タイプA
     * @param modelEmployeeWorks $Work 勤怠レコード
     * @param modelClientWorkTypes $ClientWorkType 顧客作業種別レコード
     * @param integer[] $unitPrices [標準時給, 残業時給, 深夜残業時給, 法定休日時給, 法定休日深夜残業時給]
     * @return void
     * @throws \Exception
     * 
     * 就業時間以降の時間帯については、残業時給を適用する
     */
    protected function calculateTypeA($Work, $ClientWorkType, $unitPrices)
    {
        // 金額を計算する
        try {
            $workHours = $Work->wrk_work_hours; // DateTime 勤務時間
            $wtWorkStart = $this->rukuruUtilTimeToDateTime($Work->wrk_date, $ClientWorkType->wt_work_start);    // DateTime 始業時刻
            $wtWorkEnd = $this->rukuruUtilTimeToDateTime($Work->wrk_date, $ClientWorkType->wt_work_end);    // DateTime 終業時刻
            $workStart = $this->rukuruUtilTimeToDateTime($Work->wrk_date, $Work->wrk_work_start);    // DateTime 勤怠開始時刻
            $workEnd = $this->rukuruUtilTimeToDateTime($Work->wrk_date, $Work->wrk_work_end);    // DateTime 勤怠終了時刻

            // 残業時間を計算
            $overTime = 0;  // DateInterval 残業時間
            if($workEnd > $wtWorkEnd) {
                $overTime = $workEnd->diff($wtWorkEnd);
            }
            $normalTime = $workHours->diff($overTime);    // DateInterval 通常時間
            
            // 通常時間の給与書込
            $EmployeeSalary = new modelEmployeeSalarys();
            $EmployeeSalary->employee_id = $Work->employee_id;
            $EmployeeSalary->wrk_date = $Work->wrk_date;
            $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;
            $EmployeeSalary->leave = 0;
            $EmployeeSalary->client_id = $Work->client_id;
            $EmployeeSalary->clientplace_id = $Work->clientplace_id;
            $EmployeeSalary->wt_cd = $Work->wt_cd;
            $EmployeeSalary->wrk_work_start = $Work->wrk_work_start;
            $EmployeeSalary->wrk_work_end = min($curClientWorkType->wt_work_end, $Work->wrk_work_end);
            $EmployeeSalary->wrk_work_hours = $normalTime;
            $payhour = $unitPrices['wt_pay_std'];
            $EmployeeSalary->payhour = $payhour;
            $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
            $EmployeeSalary->wrk_pay = floor($payhour * ($normalTime->h + ($normalTime->i / 60)));
            $EmployeeSalary->save();

            // 通常時間の請求書込
            $BillDetail = new modelBillDetails();
            // $BillDetail->bill_id = $Bill->id;
            // $BillDetail->display_order = $displayOrder++;
            $BillDetail->title = '明細';
            $billhour = $unitPrices['wt_bill_std'];
            $BillDetail->unit_price = $billhour;
            $BillDetail->quantity = $normalTime->h + ($normalTime->i / 60);
            $BillDetail->unit = '時間';
            $amount = floor($billhour * $BillDetail->quantity);
            $BillDetail->amount = $amount;
            $tax = floor($amount * 0.1);
            $BillDetail->tax = $tax;
            $BillDetail->total = $amount + $tax;
            $BillDetail->notes = $curClientWorkType->wt_name;
            $billDetail->save();

            // 残業時間の給与・請求書込
            if($overTime > 0)
            {
                $EmployeeSalary = new modelEmployeeSalarys();
                $EmployeeSalary->employee_id = $Work->employee_id;
                $EmployeeSalary->wrk_date = $Work->wrk_date;
                $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;
                $EmployeeSalary->leave = 0;
                $EmployeeSalary->client_id = $Work->client_id;
                $EmployeeSalary->clientplace_id = $Work->clientplace_id;
                $EmployeeSalary->wt_cd = $Work->wt_cd;
                $EmployeeSalary->wrk_work_start = $curClientWorkType->wrk_work_end;
                $EmployeeSalary->wrk_work_end = $Work->wrk_work_end;
                $EmployeeSalary->wrk_work_hours = $overTime;
                $payhour = $unitPrices['wt_pay_ovr'];
                $EmployeeSalary->payhour = $payhour;
                $EmployeeSalary->premium = 1.25;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_pay = floor($payhour * ($overTime->h + ($overlTime->i / 60)));
                $EmployeeSalary->save();

                $BillDetail = new modelBillDetails();
                // $BillDetail->bill_id = $Bill->id;
                // $BillDetail->display_order = $displayOrder++;
                $BillDetail->title = '明細';
                $billhour = $unitPrices['wt_bill_ovr'];
                $BillDetail->unit_price = $billhour;
                $BillDetail->quantity = $overTime->h + ($overTime->i / 60);
                $BillDetail->unit = '時間';
                $amount = floor($billhour * $BillDetail->quantity);
                $BillDetail->amount = $amount;
                $tax = floor($amount * 0.1);
                $BillDetail->tax = $tax;
                $BillDetail->total = $amount + $tax;
                $BillDetail->notes = $curClientWorkType->wt_name;
                $billDetail->save();
            }
        }
        catch (\Exception $e) {
            // 例外処理 
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 従業員勤怠から従業員給与、請求明細を計算
     */
    protected function calculatePayrollBill()
    {
        // work_year, work_month に該当する従業員勤怠を取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $Works = modelEmployeeWorks::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('employee_id, wrk_date, wrk_work_start')
            ->get();

        // 各種キーの初期化
        $this->saveEmployeeId = null; // 従業員ID
        $this->saveWrkDate = null;    // 勤務日
        $this->wk_ttl_seq = null;     // 1日の中の勤怠連番
        $this->saveCliendId = null;   // 顧客ID
        $this->saveClientPlaceId = null;  // 事業所ID
        $this->saveWtCd = null;   // 作業種別コード
        $this->cdHoliday = false; // 休日種別 rukuruUtilIsHoliday で設定
        // 計算に必要なレコード
        $this->curEmployee = null;
        $this->curClient = null;
        $this->curClientPlace = null;

        foreach($Works as $Work) {
            // 従業員が変わった場合
            if ($this->saveEmployeeId != $Work->employee_id) {
                $this->saveEmployeeId = $Work->employee_id;
                $this->curEmployee = modelEmployees::find($this->saveEmployeeid);  // 従業員情報を取得
                $this->saveWrkDate = null;
            }
            // 日付が変わった場合
            if ($this->saveWrkDate != $Work->wrk_date) {
                $this->saveWrkDate = $Work->wrk_date;
                $this->cdHoliday = $this->rukuruUtilIsHoliday($this->saveClientId, $this->saveWrkDate->format('Y-m-d'));
                $this->wrk_ttl_seq = 1;
            }
            // 顧客または事業所が変わった場合
            if ($this->saveCliendId != $Work->client_id || $this->saveClientPlaceId != $Work->clientplace_id) {
                $this->saveCliendId = $Work->client_id;
                $this->saveClientPlaceId = $Work->clientplace_id;
                $this->curClient = modelClients::find($saveCliendId);
                $this->curClientPlace = modelClientPlaces::find($saveClientPlaceId);
                $this->saveWtCd = null;
            }
            // 作業種別が変わった場合
            if ($this->saveWtCd != $Work->wt_cd) {
                $this->saveWtCd = $Work->wt_cd;
                // 給与、請求単価を取得
                $this->curClientWorkType = modelClientWorkTypes::getSutable($this->saveClientId, $this->saveClientPlaceId, $this->saveWtCd);
                $unitPrices = $this->rukuruUtilGetEmployeeUnitPrices(
                    $this->curClientWorkType, 
                    $this->saveEmployeeId, 
                    $this->saveClientId, 
                    $this->saveClientPlaceId, 
                    $this->saveWtCd
                );
            }
            
            // 金額を計算する
            try {
                // 金額計算の分岐
                switch($saveWtCd) {
                    case '52':
                        $this->calculateTypeA($Work, $unitPrices);
                        break;
                    default:
                        $this->calculateTypeDefault();
                        break;
                }
            }
            catch (\Exception $e) {
                // 例外処理 
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * 請求の明細を集計
     * @return void
     * @throws \Exception
     */
    protected function summaryBill()
    {
        // work_year, work_month に該当する請求を取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $BillDetails = modelBillDetails::whereBetween('bill_date', [$sStartDay, $sEndDay])
            ->get();
        foreach($Bills as $Bill) {
            $BillDetails = modelBillDetails::where('bill_id', $Bill->id)
                ->get();
            $Bill->amount = 0;
            $Bill->tax = 0;
            $Bill->total = 0;
            foreach($BillDetails as $BillDetail) {
                $Bill->amount += $BillDetail->amount;
                $Bill->tax += $BillDetail->tax;
                $Bill->total += $BillDetail->total;
            }
            $Bill->save();
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
        $this->setisClosed();
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
        $this->setisClosed();
        $this->enableCloseButton = true;
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->enableCloseButton = false;
        $this->validate();
        $this->setisClosed();
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
        DB::beginTransaction();
        try {
            // 従業員給与レコードを削除
            $this->deleteEmployeeSalary();
            // 請求と請求明細レコードを削除
            $this->deleteBill();
            // 従業員勤怠から給与と請求の明細を作成
            $this->calculatePayrollBill();
            // 給与と請求の明細を集計
            $this->summaryBill();
            // 勤怠締めレコードを更新する
            $ClosePayroll = new modelClosePayrolls();
            $ClosePayroll->updateOrCreate(
                ['work_year' => $this->workYear, 'work_month' => $this->workMonth],
                ['closed' => true, 'operation_date' => date('Y-m-d H:i:s')]
            );
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', '締め処理に失敗しました。');
            return;
        }
        Session::flash('message', '締め処理が完了しました。');
    }

    /**
     * reopen button click event
     */
    public function reopenPayroll()
    {
        // 従業員給与レコードを削除
        // $this->deleteEmployeeSalary();
        // 請求と請求明細レコードを削除
        // $this->deleteBill();
        // 勤怠締めレコードを更新する
        $ClosePayroll = new modelClosePayrolls();
        $ClosePayroll->updateOrCreate(
            ['work_year' => $this->workYear, 'work_month' => $this->workMonth],
            ['closed' => false, 'operation_date' => date('Y-m-d H:i:s')]
        );
        Session::flash('success', '解除処理が完了しました。');
    }
}
