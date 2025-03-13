<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;

use App\Models\applogs;

use App\Models\closepayrolls as modelClosePayrolls;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\employees as modelEmployees;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\bills as modelBills;
use App\Models\billdetails as modelBillDetails;

/**
 * 勤怠締め処理 請求作成
 * 
 * 従業員勤怠を元に、各種マスタを参照して、請求額計算を行う
 * 入力テーブル: EmployeeWorks
 * 出力テーブル: Bills, BillDetails
 */
class Closebills extends Component
{
    use WithPagination;
    use rukuruUtilities;

    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * closing is enabled or not
     * true if closing is enabled otherwise reopen is enabled
     */
    public $isClosed = [];

    /**
     * 顧客レコード
     */
    public $Client;

    /**
     * rules for validation
     */
    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

    // 集計キー
	protected $saveClientPlaceId = null;  // 部門ID
    protected $saveSummaryIndex = null;   // 請求項目コード
    protected $saveSummaryName = null;    // 請求項目名
    protected $saveUnitPrice = 0;   // 請求単価

    // 集計用変数
    protected $sumBillId = null;   // 請求ID
    protected $sumWorkHours = null;  // 勤務時間
    protected $sumBill = 0;  // 請求金額
    protected $sumDisplayOrder = 1;  // 表示順
    protected $sumAmount = 0;      // 請求金額
    protected $taxRate = 0;        // 消費税率

    /**
     * 請求と請求明細レコードを削除
     * @return void
     */
    protected function deleteBill()
    {
        // work_year, work_month, client_id に該当する請求と請求明細を削除
        $Bills = modelBills::where('client_id', $this->Client->id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->get();
        foreach($Bills as $Bill) {
            modelBillDetails::where('bill_id', $Bill->id)
                ->delete();
            $Bill->delete();
        }
    }

    /**
     * 請求明細レコード作成
     */
    protected function createBillDetail()
    {
        // 請求明細レコードを作成
        $BillDetail = new modelBillDetails();
        $BillDetail->bill_id = $this->sumBillId;
        $BillDetail->display_order = $this->sumDisplayOrder++;
        $BillDetail->title = $this->saveSummaryName;          // 請求項目名
        $BillDetail->unit_price = $this->saveUnitPrice; // 請求単価
        $BillDetail->quantity = 0;
        $BillDetail->quantity_string = $this->rukuruUtilDateIntervalFormat($this->sumWorkHours);    // 請求数量
        $BillDetail->unit = '時間';                     // 単位
        $BillDetail->amount = $this->sumAmount;        // 請求金額
        $BillDetail->tax = floor($this->sumAmount * $this->taxRate);  // 消費税
        $BillDetail->total = $BillDetail->amount + $BillDetail->tax;     // 税込額
        $BillDetail->save();

        $this->sumTotal += $this->sumAmount;  // 請求金額合計

        $this->sumWorkHours = new DateInterval('PT0S');    // DateInterval 勤務時間
        $this->sumAmount = 0;   // 請求金額
    }

    /**
     * 請求の明細を集計
     * @return void
     * @throws \Exception
     */
    protected function summaryBill()
    {
        // 各顧客、部門について、work_year, work_month に該当する従業員給与レコードを取得
        $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $this->Client->cl_close_day);
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        // 勤怠締日で消費税率を取得
        $this->taxRate = 0.1;

        $EmployeeWorks = modelEmployeeworks::where('client_id', $this->Client->id)
            ->whereBetween('wrk_date', [date('Y-m-d', $dtFirstDate), date('Y-m-d', $dtLastDate)])
            ->where('wrk_bill', '>', 0)
            ->orderByRaw('clientplace_id, summary_index, billhour') // 部門、作業種別、単価でソート
            ->get();

        $this->saveClientPlaceId = null;  // 部門ID
        $this->saveSummaryIndex = null;   // 請求項目コード
        $this->saveSummaryName = null;    // 請求項目名
        $this->saveUnitPrice = 0;   // 請求単価

        $this->sumBillId = null;   // 請求ID
        $this->sumWorkHours = new DateInterval('PT0S');    // DateInterval 勤務時間
        $this->sumBill = 0;   // 請求金額
        $this->sumTotal = 0;  // 請求金額合計

        $bMustWrite = false;    // 請求明細レコードを作成するかどうか

        foreach($EmployeeWorks as $EmployeeWork) {
            // 部門が変わった場合
            if ($this->saveClientPlaceId != $EmployeeWork->clientplace_id)
            {
                // 未出力の請求明細情報があるなら、請求明細レコードを作成
                if($bMustWrite)
                {
                    $this->createBillDetail();
                }
                // 請求レコードを作成
                $Bill = new modelBills();
                $Bill->bill_date = date('Y-m-d', $dtLastDate);
                $Bill->client_id = $EmployeeWork->client_id;
                $Bill->clientplace_id = $EmployeeWork->clientplace_id;
                $Bill->work_year = $this->workYear;
                $Bill->work_month = $this->workMonth;
                $Bill->bill_title = $this->workYear . '年' . $this->workMonth . '月分';
                $Bill->bill_amount = 0;  // 集計前
                $Bill->bill_tax = 0;     // 集計前
                $Bill->bill_total = 0;   // 集計前
                $Bill->save();
                $this->sumBillId = $Bill->id;

                $this->saveClientPlaceId = $EmployeeWork->clientplace_id;
                $this->saveSummaryIndex = $EmployeeWork->summary_index;
                $this->saveSummaryName = $EmployeeWork->summary_name;
                $this->saveUnitPrice = $EmployeeWork->billhour;
                $this->saveDisplayOrder = 1;

                $this->sumTotal = 0;
                $bMustWrite = false;    // 請求明細レコードを作成するかどうか
            }

            // 作業種別または単価が変わった場合
            if ($this->saveSummaryIndex != $EmployeeWork->summary_index || $this->saveUnitPrice != $EmployeeWork->billhour) 
            {
                // 未出力の請求明細情報があるなら、請求明細レコードを作成
                if($bMustWrite)
                {
                    $this->createBillDetail();
                }
                $this->saveClientPlaceId = $EmployeeWork->clientplace_id;
                $this->saveSummaryIndex = $EmployeeWork->summary_index;
                $this->saveSummaryName = $EmployeeWork->summary_name;
                $this->saveUnitPrice = $EmployeeWork->billhour;
            }
            $strWorkHours = $EmployeeWork->wrk_work_hours; // DateTime 勤務時間
            $diWorkHours = $this->rukuruUtilTimeToDateInterval($strWorkHours);
            $this->sumWorkHours = $this->rukuruUtilDateIntervalAdd($this->sumWorkHours, $diWorkHours);
            $this->sumAmount += $EmployeeWork->wrk_bill;
            $bMustWrite = true;
        }

        // 未出力の請求明細情報があるなら、請求明細レコードを作成
        if($bMustWrite)
        {
            $this->createBillDetail();
        }

        // 請求明細の合計金額を計算
        if($this->sumBillId)
        {
            $Bills = modelBills::find($this->sumBillId);
            $Bill->bill_amount = $this->sumTotal;
            $Bill->bill_tax = floor($this->sumTotal * $this->taxRate);
            $Bill->bill_total = $Bill->bill_amount + $Bill->bill_tax;
            $Bill->save();
        }
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
        // 顧客レコード
        $Clients = modelClients::orderBy('cl_cd')
            ->paginate(AppConsts::PAGINATION);

        /**
         * 顧客ごとの締め処理完了状態
         */
        $this->isClosed = [];
        foreach($Clients as $Client) {
            $this->isClosed[$Client->id] = false;
        }
        foreach($Clients as $Client) {
            $ClosePayroll = modelClosePayrolls::where('work_year', $this->workYear)
                ->where('work_month', $this->workMonth)
                ->where('client_id', $Client->id)
                ->first();
            $this->isClosed[$Client->id] = $ClosePayroll ? $ClosePayroll->closed : false;
        }

        // 対象期間文字列を作成する
        $periods = [];
        foreach($Clients as $Client) {
            $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $Client->cl_close_day);
            $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));        
            $periods[$Client->id] = date('Y/m/d', $dtFirstDate) . '～' . date('Y/m/d', $dtLastDate);
        }

        // 顧客ごとに対象人数を数える
        $employeeCount = [];
        foreach($Clients as $Client) {
            $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $Client->cl_close_day);
            $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));        
            $EmployeeWorks = modelEmployeeWorks::select('employee_id')
                ->where('client_id', $Client->id)
                ->whereBetween('wrk_date', [date('Y-m-d', $dtFirstDate), date('Y-m-d', $dtLastDate)])
                ->distinct()
                ->get();
            $employeeCount[$Client->id] = count($EmployeeWorks);
        }

        return view('livewire.closebills', compact('Clients', 'periods', 'employeeCount'));
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
     * 請求締め処理
     * @param integer $client_id 顧客ID
     * 
     * 従業員の勤怠をチェック
     * 勤怠を元に請求額を計算
     * 勤怠締めレコードを作成または更新
     */
    public function closeBill($client_id)
    {
        $this->Client = modelClients::find($client_id);

        DB::beginTransaction();
        try {
            // 請求と請求明細を削除
            $this->deleteBill();
            // 請求の明細を集計
            $this->summaryBill();
            // 勤怠締めレコードを更新する
            modelClosePayrolls::closePayroll($this->workYear, $this->workMonth, $this->Client->id);
            DB::commit();
            $logMessage = '請求締め処理: ' . $this->workYear . '年' . $this->workMonth . '月 ' . $this->Client->cl_cd . ' ' . $this->Client->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('success', '請求締め処理が完了しました。');
        }
        catch (\Exception $e) {
            DB::rollBack();
            $logMessage = '請求締め処理エラー: ' . $this->workYear . '年' . $this->workMonth . '月 ' . $this->Client->cl_cd . ' ' . $this->Client->cl_name
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('error', '請求締め処理に失敗しました。');
        }
    }

    /**
     * reopen button click event
     */
    public function reopenBill($client_id)
    {
        $this->Client = modelClients::find($client_id);

        // 勤怠締めレコードを更新する
        try {
            modelClosePayrolls::openPayroll($this->workYear, $this->workMonth, $this->Client->id);
            
            $logMessage = '請求締め解除: ' . $this->workYear . '年' . $this->workMonth . '月 ' . $this->Client->cl_cd . ' ' . $this->Client->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            Session::flash('success', '解除処理が完了しました。');
            }
        catch (\Exception $e) {
            $logMessage = '請求締め解除エラー: ' . $this->workYear . '年' . $this->workMonth . '月 ' . $this->Client->cl_cd . ' ' . $this->Client->cl_name
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('error', '解除処理に失敗しました。');
            return;
        }

    }
}
