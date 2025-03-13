<?php

namespace App\Livewire;

use DateTime;
use DateInterval;

use Livewire\Component;

use App\Models\applogs;
use App\Models\clients as modelClient;
use App\Models\clientplaces as modelClientPlace;
use App\Models\bills as modelBill;
use App\Models\billdetails as modelBillDetails;
use App\Models\employeeworks as modelEmployeeWorks;

use App\Traits\rukuruUtilities;

use App\Services\PhpSpreadsheetService;

class Billdetails extends Component
{
    use rukuruUtilities;

    // parameters
    public $bill_id;

    // related records
    public $Bill;
    public $Client;
    public $ClientPlace;
    public $BillDetails;

    /**
     * mount function
     * @param $workYear
     * @param $workMonth
     * @param $bill_id
     */
    public function mount($bill_id)
    {
        $this->bill_id = $bill_id;
        // 関連レコードを取得
        $this->Bill = modelBill::with('client', 'clientplace')
            ->find($this->bill_id);
        $this->Client = $this->Bill->client;
        $this->ClientPlace = $this->Bill->clientplace;
        // 請求明細情報を取得
        $this->BillDetails = modelBillDetails::where('bill_id', $this->bill_id)->get();
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.billdetails');
    }

    /**
     * 請求書ダウンロード
     */
    public function downloadBill()
    {
        $service = new PhpSpreadsheetService();

        try {
            $ret =  $service->exportBill(
                storage_path('data/bill_template.xlsx'),
                [
                    'bill_date' => $this->Bill->bill_date,
                    'bill_no' => $this->Bill->bill_no,
                    'cl_name' => $this->Client->cl_full_name,
                    'bill_title' => $this->Bill->bill_title,
                    'bill_amount' => $this->Bill->bill_amount,
                    'bill_tax' => $this->Bill->bill_tax,
                    'bill_total' => $this->Bill->bill_total,
                ],
                $this->BillDetails
            );
            $logMessage = '請求書 作成: ' . $this->Bill->bill_date . ' ' . $this->Client->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('success', '請求書を作成しました。');
            return $ret;
        } catch (\Exception $e) {
            $logMessage = '請求書 作成エラー: ' . $this->Bill->bill_date . ' ' . $this->Client->cl_name
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('error', '請求書情報が取得できませんでした。');
        }
    }

    /**
     * 請求明細を集計する
     * @param $dtFirstDate
     * @param $dtLastDate
     * @return array
     */
    private function aggregateBillDetails($dtFirstDate, $dtLastDate)
    {
        $Query = modelEmployeeWorks::with('employee')
            ->select('employees.*', 'employeeworks.*')
            ->join('employees', 'employees.id', '=', 'employeeworks.employee_id')
            ->where('client_id', $this->Bill->client_id)
            ->where('clientplace_id', $this->Bill->clientplace_id)
            ->whereBetween('wrk_date', [date('Y-m-d', $dtFirstDate), date('Y-m-d', $dtLastDate)])
            ->where('wrk_bill', '>' , 0)
            ->orderByRaw('employees.empl_cd, summary_index, billhour');
        $EmployeeWorks = $Query->get();

        // 集計キー
        $saveEmployeeId = null;
        $saveEmployeeName = null;
        $saveSummaryIndex = null;
        $saveSummaryName = null;
        $saveBillHour = null;

        // 集計結果
        $sumWorkHours = new DateInterval('PT0S');
        $sumUnitPrice = 0;
        $sumBillAmount = 0;

        $BillDetails = [];  // 請求詳細集計結果

        $bWrite = false;    // 集計結果を書き込むかどうか

        foreach($EmployeeWorks as $EmployeeWork)
        {
            if($saveEmployeeId != $EmployeeWork->employee_id
            || $saveSummaryIndex != $EmployeeWork->summary_index
            || $saveBillHour != $EmployeeWork->billhour)
            {
                // 集計結果を書き込む
                if($bWrite)
                {
                    $BillDetails[] = [
                        'employee_id' => $saveEmployeeId,
                        'empl_name' => $saveEmployeeName,
                        'summary_index' => $saveSummaryIndex,
                        'summary_name' => $saveSummaryName,
                        'billhour' => $this->rukuruUtilDateIntervalFormat($sumWorkHours),
                        'unit_price' => $sumUnitPrice,
                        'bill_amount' => $sumBillAmount,
                    ];
                }

                // 集計結果を初期化
                $sumWorkHours = new DateInterval('PT0S');
                $sumUnitPrice = $EmployeeWork->billhour;
                $sumBillAmount = 0;

                // 集計キーを更新
                $saveEmployeeId = $EmployeeWork->employee_id;
                $saveEmployeeName = $EmployeeWork->employee->empl_name_last . ' ' . $EmployeeWork->employee->empl_name_first;
                $saveSummaryIndex = $EmployeeWork->summary_index;
                $saveSummaryName = $EmployeeWork->summary_name;
                $saveBillHour = $EmployeeWork->billhour;
                $bWrite = false;
            }

            // 集計結果を更新
            $workHours = $this->rukuruUtilTimeToDateInterval($EmployeeWork->wrk_work_hours);
            $sumWorkHours = $this->rukuruUtilDateIntervalAdd($sumWorkHours, $workHours);
            $sumBillAmount += $this->rukuruUtilMoneyValue($EmployeeWork->wrk_bill, 0);
            $bWrite = true;
        }

        // 集計結果を書き込む
        if($bWrite)
        {
            $BillDetails[] = [
                'employee_id' => $saveEmployeeId,
                'empl_name' => $saveEmployeeName,
                'summary_index' => $saveSummaryIndex,
                'summary_name' => $saveSummaryName,
                'billhour' => $this->rukuruUtilDateIntervalFormat($sumWorkHours),
                'unit_price' => $sumUnitPrice,
                'bill_amount' => $sumBillAmount,
            ];
        }

        return $BillDetails;
    }

    /**
     * 請求明細ダウンロード
     */
    public function downloadBillDetails()
    {
        $service = new PhpSpreadsheetService();

        try{
            // 対象年月
            $workYear = $this->Bill->work_year;
            $workMonth = $this->Bill->work_month;
            $workYearMonth = $workYear . '年' . $workMonth . '月';
            // 対象期間
            $dtFirstDate = $this->rukuruUtilGetStartDate($workYear, $workMonth, $this->Client->cl_close_day);
            $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

            // 請求明細を集計する
            $BillDetails = $this->aggregateBillDetails($dtFirstDate, $dtLastDate);

            $clientInfo = [
                'cl_name' => $this->Client->cl_full_name,
                'cl_place_name' => $this->ClientPlace->cl_pl_name,
                'work_year' => $workYear,
                'work_month' => $workMonth,
                'first_date' => date('Y/m/d', $dtFirstDate),
                'last_date' => date('Y/m/d', $dtLastDate),
            ];
    
            $ret = $service->exportBillDetails(
                storage_path('data/billdetails_template.xlsx'),
                $clientInfo,
                $BillDetails
            );
            $logMessage = '請求明細 作成: ' . $this->Bill->bill_date . ' ' . $this->Client->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('error', '請求明細を作成しました。');
            return $ret;
        }
        catch (\Exception $e) {
            $logMessage = '請求明細 作成エラー: ' . $this->Bill->bill_date . ' ' . $this->Client->cl_name
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_BILL, $logMessage);
            session()->flash('error', '請求明細情報が取得できませんでした。');
        }

    }

    /**
     * cancel bill details
     */
    public function cancelBillDetails()
    {
        // redirect to workemployees
        return redirect()->route('bills');
    }
}
