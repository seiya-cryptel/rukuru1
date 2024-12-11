<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\clients as modelClient;
use App\Models\clientplaces as modelClientPlace;
use App\Models\bills as modelBill;
use App\Models\billdetails as modelBillDetails;
use App\Models\employeesalarys as modelEmployeeSalarys;

use App\Services\PhpSpreadsheetService;

class Billdetails extends Component
{
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
        session()->flash('success', '請求書を作成します。');
        return $service->exportBill(
            storage_path('data/bill_template.xlsx'),
            // 'storage/data/bill_template.xlsx',
            [
                'bill_date' => $this->Bill->bill_date,
                'bill_no' => $this->Bill->bill_no,
                'cl_name' => $this->Client->cl_name,
            ],
            $this->BillDetails
        );
    }

    /**
     * 請求明細ダウンロード
     */
    public function downloadBillDetails()
    {
        $service = new PhpSpreadsheetService();
        session()->flash('success', '請求明細を作成します。');

        // 各顧客、事業所について、work_year, work_month に該当する従業員給与レコードを取得
        $workYear = $this->Bill->work_year;
        $workMonth = $this->Bill->work_month;
        $sStartDay = $workYear . '-' . $workMonth . '-01';
        $sEndDay = $workYear . '-' . $workMonth . '-' . date('t', strtotime($sStartDay));
        $EmployeeSalarys = modelEmployeeSalarys::with('employee')
            ->where('client_id', $this->Bill->client_id)
            ->where('clientplace_id', $this->Bill->clientplace_id)
            ->whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('wrk_date, employee_id, wrk_work_start')
            ->get();

            $clientInfo = [
                'cl_name' => $this->Client->cl_name,
                'cl_place_name' => $this->ClientPlace->cl_place_name,
                'work_year' => $workYear,
                'work_month' => $workMonth,
            ];

        return $service->exportBillDetails(
            storage_path('data/billdetails_template.xlsx'),
            $clientInfo,
            $EmployeeSalarys
        );
    }

    /**
     * cancel bill details
     */
    public function cancelBillDetails()
    {
        // セッション変数にキーを設定する
        session(['workYear' => $this->Bill->workYear]);
        session(['workMonth' => $this->Bill->workMonth]);
        session(['client_id' => $this->Bill->client_id]);
        session(['clientplace_id' => $this->Bill->clientplace_id]);

        // redirect to workemployees
        return redirect()->route('bills');
    }
}
