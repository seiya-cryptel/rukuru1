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

use App\Models\closepayrolls as modelClosePayrolls;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorkTypes;

use App\Models\employees as modelEmployees;
use App\Models\employeepays as modelEmployeePays;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeesalarys as modelEmployeeSalarys;

use App\Models\bills as modelBills;
use App\Models\billdetails as modelBillDetails;
use App\Models\pricetables as modelPriceTables;

use App\Models\salary as modelSalary;

/**
 * 勤怠締め処理 請求作成
 * 
 * 従業員勤怠を元に、各種マスタを参照して、請求額計算を行う
 */
class Closebills extends Component
{
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

	protected $saveEmployeeId = null; // 従業員ID
	protected $saveWrkDate = null;    // 勤務日
	protected $wk_ttl_seq = null;     // 1日の中の勤怠連番
	protected $saveClientId = null;   // 顧客ID
	protected $saveClientPlaceId = null;  // 部門ID
    protected $saveDisplayOrder = 1;  // 表示順
    protected $saveTitle = null;      // 請求項目名
    protected $saveUnitPrice = 0;   // 請求単価
    protected $saveQuantity = 0;    // 請求数量
    protected $saveAmount = 0;      // 請求金額
	protected $saveWtCd = null;   // 作業種別コード
	protected $cdHoliday = false; // 休日種別 rukuruUtilIsHoliday で設定
    protected $saveBillId = null;   // 請求ID
    protected $saveWtBillItemCd = null;   // 請求項目コード

    // 計算に必要なレコード
	protected $curEmployee = null;
	protected $curClient = null;
	protected $curClientPlace = null;
    protected $curClientWorkType = null;

    /**
     * 従業員給与レコードを削除
     * @param integer $client_id 顧客ID
     */
    protected function deleteEmployeeSalary()
    {
        // work_year, work_month, client_id に該当する従業員給与を削除
        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-' .  ($this->Client->cl_close_day + 1));
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));        
        $sStartDay = date('Y-m-d', $dtFirstDate);
        $sEndDay = date('Y-m-d', $dtLastDate);
        modelEmployeeSalarys::where('client_id', $this->Client->id)
            ->whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->delete();
    }

    /**
     * 請求と請求明細レコードを削除
     */
    protected function deleteBillSalary()
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
        $Bills = modelSalary::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->delete();
    }

    /**
     * 給与と請求の計算：標準
     * @param modelEmployeeWorks $Work 勤怠レコード
     * @param integer[] $hourlyRates [標準時給, 残業時給, 深夜残業時給, 法定休日時給, 法定休日深夜残業時給]
     * @return void
     * @throws \Exception
     * 
     * 就業時間を超えるの時間については、残業時給を適用する
     */
    protected function calculateDefault($Work, $hourlyRates)
    {
        // 金額計算
        try {
            $dtWrkDate = new DateTime($Work->wrk_date);    // DateTime 勤務日
            $strWorkHours = $Work->wrk_work_hours; // DateTime 勤務時間
            $dtWtWorkStart = $this->rukuruUtilTimeToDateTime($dtWrkDate, $this->curClientWorkType->wt_work_start);    // DateTime 始業時刻
            $dtWtWorkEnd = $this->rukuruUtilTimeToDateTime($dtWrkDate, $this->curClientWorkType->wt_work_end);    // DateTime 終業時刻
            $diWorkTime = $this->rukuruUtilTimeToDateInterval($strWorkHours);    // DateInterval 勤務時間
            $diWorkTypeTime = $this->rukuruUtilWorkHours(
                $dtWrkDate, 
                $dtWtWorkStart,
                $dtWtWorkEnd,
                $this->curClientWorkType);    // DateInterval 作業種別の作業時間
            $dtWorkStart = new DateTime($Work->wrk_work_start);    // DateTime 勤怠開始時刻
            $dtWorkEnd = new DateTime($Work->wrk_work_end);    // DateTime 勤怠終了時刻

            // 通常時間と残業時間を計算
            $diDiff = $this->rukuruUtilDateIntervalSub($diWorkTime, $diWorkTypeTime);    // DateInterval 作業種別の作業時間との差
            if($diDiff->invert)
            {   // 作業種別の作業時間以内の場合
                $diNormalTime = $diWorkTime;    // DateInterval 通常時間
                $diOverTime = new DateInterval('PT0H0M');   // DateInterval 残業時間
                $dtWorkEndSalary = $dtWorkEnd;   // 勤怠終了時刻を従業員給与レコードの勤怠終了時刻とする
            }
            else
            {   // 作業種別の作業時間を超えた場合
                $diNormalTime = $diWorkTypeTime;    // DateInterval 通常時間
                $diOverTime = $diDiff;   // DateInterval 残業時間
                $dtWorkEndSalary = $dtWtWorkEnd;   // 終業時刻を従業員給与レコードの勤怠終了時刻とする
            }
            
            // 通常時間の給与書込
            $EmployeeSalary = new modelEmployeeSalarys();
            $EmployeeSalary->employee_id = $Work->employee_id;      // 従業員ID
            $EmployeeSalary->wrk_date = $Work->wrk_date;            // 勤務日
            $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;    // 1日の中の勤怠連番
            $EmployeeSalary->leave = 0;
            $EmployeeSalary->client_id = $Work->client_id;          // 顧客ID
            $EmployeeSalary->clientplace_id = $Work->clientplace_id;    // 部門ID

            $EmployeeSalary->wt_cd = $Work->wt_cd;                  // 作業種別コード
            $EmployeeSalary->wrk_work_start = $dtWorkStart;    // 勤怠開始時刻
            $EmployeeSalary->wrk_work_end = $dtWorkEndSalary;   // 勤怠終了時刻
            $EmployeeSalary->wrk_work_hours = $diNormalTime->format('%H:%I:0');

            $payhour = $hourlyRates['wt_pay_std'];       // 標準時給
            $EmployeeSalary->payhour = $payhour;
            $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
            $EmployeeSalary->wrk_pay = floor($payhour * ($diNormalTime->h + ($diNormalTime->i / 60)));

            $billhour = $hourlyRates['wt_bill_std'];    // 標準請求時給
            $EmployeeSalary->wt_bill_item_cd = $this->curClientWorkType->wt_cd;      // 請求項目コード = 作業種別コード
            $EmployeeSalary->wt_bill_item_name = $this->curClientWorkType->wt_name;  // 請求項目名 = 作業種別名
            $EmployeeSalary->billhour = $billhour ? $billhour : null;
            $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
            $EmployeeSalary->wrk_bill = $billhour ? floor($billhour * ($diNormalTime->h + ($diNormalTime->i / 60))) : 0;

            $EmployeeSalary->save();

            // 残業時間の給与・請求書込
            if($diOverTime->h > 0 || $diOverTime->i > 0)
            {
                $EmployeeSalary = new modelEmployeeSalarys();
                $EmployeeSalary->employee_id = $Work->employee_id;
                $EmployeeSalary->wrk_date = $Work->wrk_date;
                $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;
                $EmployeeSalary->leave = 0;
                $EmployeeSalary->client_id = $Work->client_id;
                $EmployeeSalary->clientplace_id = $Work->clientplace_id;

                $EmployeeSalary->wt_cd = $Work->wt_cd;
                $EmployeeSalary->wrk_work_start = $dtWtWorkEnd;   // 勤怠開始時刻は終業時刻
                $EmployeeSalary->wrk_work_end = $dtWorkEnd;   // 勤怠終了時刻
                $EmployeeSalary->wrk_work_hours = $diOverTime->format('%H:%I:0');

                $payhour = $hourlyRates['wt_pay_ovr'];
                $EmployeeSalary->payhour = $payhour;
                $EmployeeSalary->premium = 1.25;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_pay = floor($payhour * ($diOverTime->h + ($diOverTime->i / 60)));

                $billhour = $hourlyRates['wt_bill_ovr'];    // 残業請求時給
                $EmployeeSalary->wt_bill_item_cd = $this->curClientWorkType->wt_cd;      // 請求項目コード = 作業種別コード
                $EmployeeSalary->wt_bill_item_name = $this->curClientWorkType->wt_name . '残業';  // 請求項目名 = 作業種別名
                $EmployeeSalary->billhour = $billhour ? $billhour : null;
                $EmployeeSalary->premium = 1.25;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_bill = $billhour ? floor($billhour * ($diOverTime->h + ($diOverTime->i / 60))) : 0;

                $EmployeeSalary->save();
            }
        }
        catch (\Exception $e) {
            // 例外処理 
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 給与と請求の計算：タイプA
     * @param modelEmployeeWorks $Work 勤怠レコード
     * @param integer[] $hourlyRates [標準時給, 残業時給, 深夜残業時給, 法定休日時給, 法定休日深夜残業時給]
     * @return void
     * @throws \Exception
     * 
     * 就業時間以降の時間帯については、残業時給を適用する
     */
    protected function calculateTypeA($Work, $hourlyRates)
    {
        // 金額を計算する
        try {
            $dtWrkDate = new DateTime($Work->wrk_date);    // DateTime 勤務日
            $strWorkHours = $Work->wrk_work_hours; // DateTime 勤務時間
            $dtWtWorkStart = $this->rukuruUtilTimeToDateTime($dtWrkDate, $this->curClientWorkType->wt_work_start);    // DateTime 始業時刻
            $dtWtWorkEnd = $this->rukuruUtilTimeToDateTime($dtWrkDate, $this->curClientWorkType->wt_work_end);    // DateTime 終業時刻
            $dtWorkStart = new DateTime($Work->wrk_work_start);    // DateTime 勤怠開始時刻
            $dtWorkEnd = new DateTime($Work->wrk_work_end);    // DateTime 勤怠終了時刻

            // 通常時間と残業時間を計算
            $diNormalTime = $dtWorkStart->diff(min($dtWorkEnd, $dtWtWorkEnd));    // DateInterval 通常時間
            $diOverTime = ($dtWorkEnd > $dtWtWorkEnd) ? $dtWtWorkEnd->diff($dtWorkEnd) : new DateInterval('PT0H0M');   // DateInterval 残業時間
            
            // 通常時間の給与書込
            $EmployeeSalary = new modelEmployeeSalarys();
            $EmployeeSalary->employee_id = $Work->employee_id;      // 従業員ID
            $EmployeeSalary->wrk_date = $Work->wrk_date;            // 勤務日
            $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;    // 1日の中の勤怠連番
            $EmployeeSalary->leave = 0;
            $EmployeeSalary->client_id = $Work->client_id;          // 顧客ID
            $EmployeeSalary->clientplace_id = $Work->clientplace_id;    // 部門ID

            $EmployeeSalary->wt_cd = $Work->wt_cd;                  // 作業種別コード
            $EmployeeSalary->wrk_work_start = $dtWorkStart;    // 勤怠開始時刻
            $EmployeeSalary->wrk_work_end = min($dtWorkEnd, $dtWtWorkEnd);
            $EmployeeSalary->wrk_work_hours = $diNormalTime->format('%H:%I:0');

            $payhour = $hourlyRates['wt_pay_std'];       // 標準時給
            $EmployeeSalary->payhour = $payhour;
            $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
            $EmployeeSalary->wrk_pay = floor($payhour * ($diNormalTime->h + ($diNormalTime->i / 60)));

            $billhour = $hourlyRates['wt_bill_std'];    // 標準請求時給
            $EmployeeSalary->wt_bill_item_cd = $this->curClientWorkType->wt_cd;      // 請求項目コード = 作業種別コード
            $EmployeeSalary->wt_bill_item_name = $this->curClientWorkType->wt_name;  // 請求項目名 = 作業種別名
            $EmployeeSalary->billhour = $billhour ? $billhour : null;
            $EmployeeSalary->premium = 1.0;    // ToDo: プレミアムを計算
            $EmployeeSalary->wrk_bill = $billhour ? floor($billhour * ($diNormalTime->h + ($diNormalTime->i / 60))) : 0;

            $EmployeeSalary->save();

            // 残業時間の給与・請求書込
            if($diOverTime->h > 0 || $diOverTime->i > 0)
            {
                $EmployeeSalary = new modelEmployeeSalarys();
                $EmployeeSalary->employee_id = $Work->employee_id;
                $EmployeeSalary->wrk_date = $Work->wrk_date;
                $EmployeeSalary->wrk_ttl_seq = $this->wrk_ttl_seq++;
                $EmployeeSalary->leave = 0;
                $EmployeeSalary->client_id = $Work->client_id;
                $EmployeeSalary->clientplace_id = $Work->clientplace_id;

                $EmployeeSalary->wt_cd = $Work->wt_cd;
                $EmployeeSalary->wrk_work_start = $dtWtWorkEnd;
                $EmployeeSalary->wrk_work_end = $dtWorkEnd;
                $EmployeeSalary->wrk_work_hours = $diOverTime->format('%H:%I:0');

                $payhour = $hourlyRates['wt_pay_ovr'];
                $EmployeeSalary->payhour = $payhour;
                $EmployeeSalary->premium = 1.25;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_pay = floor($payhour * ($diOverTime->h + ($diOverTime->i / 60)));

                $billhour = $hourlyRates['wt_bill_ovr'];    // 残業請求時給
                $EmployeeSalary->wt_bill_item_cd = $this->curClientWorkType->wt_cd;      // 請求項目コード = 作業種別コード
                $EmployeeSalary->wt_bill_item_name = $this->curClientWorkType->wt_name . '残業';  // 請求項目名 = 作業種別名
                $EmployeeSalary->billhour = $billhour ? $billhour : null;
                $EmployeeSalary->premium = 1.25;    // ToDo: プレミアムを計算
                $EmployeeSalary->wrk_bill = $billhour ? floor($billhour * ($diOverTime->h + ($diOverTime->i / 60))) : 0;

                $EmployeeSalary->save();
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
            ->orderByRaw('employee_id, wrk_date, wrk_work_start')   // DateTime wrk_work_start
            ->get();

        // 各種キーの初期化
        $this->saveEmployeeId = null; // 従業員ID
        $this->saveWrkDate = null;    // 勤務日
        $this->wk_ttl_seq = null;     // 1日の中の勤怠連番
        $this->saveClientId = null;   // 顧客ID
        $this->saveClientPlaceId = null;  // 部門ID
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
                $this->curEmployee = modelEmployees::find($this->saveEmployeeId);  // 従業員情報を取得
                $this->saveWrkDate = null;
            }
            // 日付が変わった場合
            if ($this->saveWrkDate != $Work->wrk_date) {
                $this->saveWrkDate = $Work->wrk_date;
                $this->cdHoliday = $this->rukuruUtilIsHoliday($Work->client_id, $this->saveWrkDate);
                $this->wrk_ttl_seq = 1;
            }
            // 顧客または部門が変わった場合
            if ($this->saveClientId != $Work->client_id || $this->saveClientPlaceId != $Work->clientplace_id) {
                $this->saveClientId = $Work->client_id;
                $this->saveClientPlaceId = $Work->clientplace_id;
                $this->curClient = modelClients::find($this->saveClientId);
                $this->curClientPlace = modelClientPlaces::find($this->saveClientPlaceId);
                $this->saveWtCd = null;
            }
            // 作業種別が変わった場合
            if ($this->saveWtCd != $Work->wt_cd) {
                $this->saveWtCd = $Work->wt_cd;
                // 給与、請求単価を取得
                $this->curClientWorkType = modelClientWorkTypes::getSutable($this->saveClientId, $this->saveClientPlaceId, $this->saveWtCd);
                $hourlyRates = $this->rukuruUtilGetEmployeeHourlyRates(
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
                switch($this->saveWtCd) {
                    case '51':
                    case '52':
                    case '53':
                        $this->calculateTypeA($Work, $hourlyRates);
                        break;
                    default:
                        $this->calculateDefault($Work, $hourlyRates);
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
     * 請求明細レコード作成
     */
    protected function createBillDetail()
    {
        if($this->saveWtBillItemCd == null || $this->saveAmount == 0) {
            return;
        }
        // 請求明細レコードを作成
        $BillDetail = new modelBillDetails();
        $BillDetail->bill_id = $this->saveBillId;
        // $BillDetail->wt_bill_item_cd = $this->saveWtBillItemCd;
        $BillDetail->display_order = $this->saveDisplayOrder++;
        $BillDetail->title = $this->saveTitle;          // 請求項目名
        $BillDetail->unit_price = $this->saveUnitPrice; // 請求単価
        $BillDetail->quantity = $this->saveQuantity;    // 請求数量
        $BillDetail->unit = '時間';                     // 単位
        $BillDetail->amount = $this->saveAmount;        // 請求金額
        $BillDetail->tax = floor($this->saveAmount * 0.1);  // 消費税
        $BillDetail->total = floor($this->saveAmount * 1.1);     // 集計前
        $BillDetail->save();

        $this->saveWtBillItemCd = null;
        $this->saveUnitPrice = 0;
        $this->saveQuantity = 0;
        $this->saveAmount = 0;
    }

    /**
     * 請求の明細を集計
     * @return void
     * @throws \Exception
     */
    protected function summaryBill()
    {
        // 各顧客、部門について、work_year, work_month に該当する従業員給与レコードを取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $EmployeeSalarys = modelEmployeeSalarys::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('client_id, clientplace_id, wt_bill_item_cd, billhour')
            ->get();

        $this->saveClientId = null;   // 顧客ID
        $this->saveClientPlaceId = null;  // 部門ID
        $this->saveWtBillItemCd = null;   // 請求項目コード
    
        foreach($EmployeeSalarys as $EmployeeSalary) {
            // 顧客または部門が変わった場合
            if ($this->saveClientId != $EmployeeSalary->client_id || $this->saveClientPlaceId != $EmployeeSalary->clientplace_id) {
                // 未出力の請求明細情報があるなら、請求明細レコードを作成
                if($this->saveWtBillItemCd)
                {
                    $this->createBillDetail();
                }
                // 請求レコードを作成
                $Bill = new modelBills();
                $Bill->client_id = $EmployeeSalary->client_id;
                $Bill->clientplace_id = $EmployeeSalary->clientplace_id;
                $Bill->work_year = $this->workYear;
                $Bill->work_month = $this->workMonth;
                $Bill->bill_title = $this->workYear . '年' . $this->workMonth . '月分';
                $Bill->bill_amount = 0;  // 集計前
                $Bill->bill_tax = 0;     // 集計前
                $Bill->bill_total = 0;   // 集計前
                $Bill->save();
                $this->saveBillId = $Bill->id;

                $this->saveClientId = $EmployeeSalary->client_id;
                $this->saveClientPlaceId = $EmployeeSalary->clientplace_id;
                // $this->saveWtBillItemCd = null;
                $this->saveDisplayOrder = 1;
            }

            // 作業種別または単価が変わった場合
            if ($this->saveWtBillItemCd != $EmployeeSalary->wt_bill_item_cd || $this->saveUnitPrice != $EmployeeSalary->billhour) {
                // 未出力の請求明細情報があるなら、請求明細レコードを作成
                if($this->saveWtBillItemCd)
                {
                    $this->createBillDetail();
                }
                $this->saveWtBillItemCd = $EmployeeSalary->wt_bill_item_cd;
                $this->saveUnitPrice = $EmployeeSalary->billhour;
                $this->saveTitle = $EmployeeSalary->wt_bill_item_name;
            }
            // $this->saveUnitPrice = $EmployeeSalary->billhour;
            $strWorkHours = $EmployeeSalary->wrk_work_hours; // DateTime 勤務時間
            $diWorkHours = $this->rukuruUtilTimeToDateInterval($EmployeeSalary->wrk_work_hours);
            $this->saveQuantity += round($diWorkHours->h + ($diWorkHours->i / 60), 2);
            $this->saveAmount += $EmployeeSalary->wrk_bill;
        }

        // 未出力の請求明細情報があるなら、請求明細レコードを作成
        if($this->saveWtBillItemCd)
        {
            $this->createBillDetail();
        }

        // 請求明細の合計金額を計算
        // work_year, work_month に該当する請求を取得
        $Bills = modelBills::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->get();
        foreach($Bills as $Bill) {
            $BillSummary = modelBillDetails::where('bill_id', $Bill->id)
                ->selectRaw('sum(amount) as amount, sum(tax) as tax, sum(total) as total')
                ->first();
            $Bill->bill_amount = $BillSummary->amount;
            $Bill->bill_tax = $BillSummary->tax;
            $Bill->bill_total = $BillSummary->total;
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

        return view('livewire.closebills', compact('Clients'));
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
     * 請求締め処理
     * @param integer $client_id 顧客ID
     * 
     * 従業員の勤怠をチェック
     * 勤怠を元に支給額を計算
     * 勤怠を元に請求額を計算
     * 勤怠締めレコードを作成または更新
     */
    public function closePayroll($client_id)
    {
        $this->Client = modelClients::find($client_id);

        DB::beginTransaction();
        try {
            // 従業員給与レコードを削除
            $this->deleteEmployeeSalary();
            // 請求と請求明細、従業員支給額レコードを削除
            $this->deleteBillSalary();
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
            session()->flash('error', '締め処理に失敗しました。');
            return;
        }
        $this->setisClosed();
        Session::flash('success', '締め処理が完了しました。');
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
        $this->setisClosed();
        Session::flash('success', '解除処理が完了しました。');
    }
}
