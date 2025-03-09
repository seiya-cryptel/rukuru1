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
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\EmployeeWork as modelEmployeeWork;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;

use App\Models\bills as modelBills;
use App\Models\billdetails as modelBillDetails;

use App\Models\salarys as modelSalarys;

use App\Models\masterallowdeducts as modelMasterAllowDeducts;

/**
 * 勤怠締め処理
 * 
 * 従業員勤怠を元に、各種マスタを参照して、給与と請求の計算を行う
 * 入力テーブル: Empoyeeworks
 * 出力テーブル: Salarys
 */
class Closepayrolls extends Component
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
    public $isClosed;

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
        // $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-' .  ($this->Client->cl_close_day + 1));
        $dtFirstDate = $this->rukuruUtilGetStartDate($this->workYear, $this->workMonth, $this->Client->cl_close_day);
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));        
        $sStartDay = date('Y-m-d', $dtFirstDate);
        $sEndDay = date('Y-m-d', $dtLastDate);
        modelEmployeeWork::where('client_id', $this->Client->id)
            ->whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->delete();
    }

    /**
     * 請求と請求明細レコードを削除
     */
    protected function deleteSalary()
    {
        $Bills = modelSalary::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->delete();
    }

    
    /**
     * 給与レコード作成
     */
    protected function createSalary()
    {
        // 請求明細レコードを作成
        $Salary = new modelSalarys();
        $Salary->employee_id = $this->saveEmployeeId;
        $Salary->work_year = $this->workYear;
        $Salary->work_month = $this->workMonth;
        $Salary->work_amount = $this->kintaiAmount;
        $Salary->transport = $this->transport;
        $Salary->allow_amount = $this->allowAmount;
        $Salary->deduct_amount = $this->deductAmount;
        $Salary->pay_amount = $this->kintaiAmount + $this->transport + $this->allowAmount - $this->deductAmount;
        $Salary->save();

        $this->saveEmployeeId = null;
        $this->kintaiAmount = 0;
        $this->transport = 0;
        $this->saveAmount = 0;
    }

    /**
     * 給与集計
     * @return void
     * @throws \Exception
     */
    protected function summarySalary()
    {
        // work_year, work_month に該当する従業員勤怠レコードを取得
        $sStartDay = $this->workYear . '-' . $this->workMonth . '-01';
        $sEndDay = $this->workYear . '-' . $this->workMonth . '-' . date('t', strtotime($sStartDay));
        $EmployeeWorks = modelEmployeeWork::whereBetween('wrk_date', [$sStartDay, $sEndDay])
            ->orderByRaw('employee_id, wrk_date, wrk_seq')
            ->get();

        // 各種キーの初期化
        $this->saveEmployeeId = null; // 従業員ID
       
        // 集計値の初期化
        $this->kintaiAmount = 0;    // 勤怠金額
        $this->transport = 0;    // 交通費
        $this->allowAmount = 0;    // 手当金額
        $this->deductAmount = 0;    // 控除金額

        $bMustWrite = false;    // 給与レコードを作成するかどうか

        foreach($EmployeeWorks as $EmployeeWork) {
            // 従業員が変わった場合
            if ($this->saveEmployeeId != $EmployeeWork->employee_id) {
                // 未出力の請求明細情報があるなら、請求明細レコードを作成
                if($this->bMustWrite)
                {
                    $this->createSalary();
                }
                $this->saveEmployeeId = $EmployeeWork->employee_id;

                $this->kintaiAmount = 0;    // 勤怠金額
                $this->transport = 0;    // 交通費
                $this->allowAmount = 0;    // 手当金額
                $this->deductAmount = 0;    // 控除金額
                $bMustWrite = false;    // 給与レコードを作成するかどうか
            }
            $this->kintaiAmount += $EmployeeWork->wrk_pay;    // 勤怠金額
            $this->transport += modelEmployeeAllowDeduct::getAllowAmount($EmployeeWork->employee_id, $this->work_year, $this->work_month, AppConsts::MAD_CD_TRANSPORT);    // 交通費
            $this->allowAmount += modelEmployeeAllowDeduct::getAllowAmount($EmployeeWork->employee_id, $this->work_year, $this->work_month);    // 手当金額
            $this->deductAmount += modelEmployeeAllowDeduct::getDeductAmount($EmployeeWork->employee_id, $this->work_year, $this->work_month);    // 控除金額
            $bMustWrite = true;
        }

        // 未出力の請求明細情報があるなら、請求明細レコードを作成
        if($bMustWrite)
        {
            $this->createSalary();
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
        /**
         * 給与締め処理完了状態
         */
        $ClosePayroll = modelClosePayrolls::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->where('client_id', 0)
            ->first();

        $this->isClosed = $ClosePayroll && $ClosePayroll->closed;

        return view('livewire.closepayrolls');
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
     * close button click event
     * @param integer $client_id 顧客ID
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
            // 従業員支給額レコードを削除
            $this->deleteSalary();
            // 給与を集計
            $this->summarySalary();
            // 勤怠締めレコードを更新する
            $ClosePayroll = new modelClosePayrolls();
            $ClosePayroll->updateOrCreate(
                ['work_year' => $this->workYear, 'work_month' => $this->workMonth],
                ['closed' => true, 'operation_date' => date('Y-m-d H:i:s')]
            );
            DB::commit();
            $logMessage = '給与締め処理: ' . $this->workYear . '年' . $this->workMonth . '月';
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_PAYROLL, $logMessage);
            session()->flash('success', '給与締め処理が完了しました。');
        }
        catch (\Exception $e) {
            DB::rollBack();
            $logMessage = '給与締め処理エラー: ' . $this->workYear . '年' . $this->workMonth . '月'
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_PAYROLL, $logMessage);
            session()->flash('error', '締め処理に失敗しました。');
        }
    }

    /**
     * reopen button click event
     */
    public function reopenPayroll()
    {
        $ClosePayroll = new modelClosePayrolls();
        $ClosePayroll->updateOrCreate(
            ['work_year' => $this->workYear, 'work_month' => $this->workMonth],
            ['closed' => false, 'operation_date' => date('Y-m-d H:i:s')]
        );
        $this->setisClosed();

        $logMessage = '給与締め解除: ' . $this->workYear . '年' . $this->workMonth . '月';
        logger($logMessage);
        applogs::insertLog(applogs::LOG_TYPE_CLOSE_PAYROLL, $logMessage);
        Session::flash('success', '解除処理が完了しました。');
    }
}
