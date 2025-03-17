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
use App\Models\clients as clients;
use App\Models\employees;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;
use App\Models\salary as modelSalary;

/**
 * 給与締め処理
 * 
 * 従業員勤怠を元に、各種マスタを参照して、給与支給額の計算を行う
 * 入力テーブル: Empoyeeworks
 * 出力テーブル: Salarys
 */
class Closepayrolls extends Component
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
	protected $saveWtCd = null;   // 作業種別コード
	protected $cdHoliday = false; // 休日種別 rukuruUtilIsHoliday で設定
    protected $saveBillId = null;   // 請求ID
    protected $saveWtBillItemCd = null;   // 請求項目コード

    // 計算に必要なレコード
	protected $curEmployee = null;
	protected $curClient = null;
	protected $curClientPlace = null;
    protected $curClientWorkType = null;

    // 勤怠のエラー表示用
    public $KintaiErrors = [];

    /**
     * 勤怠チェック
     * @return void
     * @throws \Exception
     * 
     * エラー条件
     * 　開始時刻か終了時刻が空
     */
    protected function checkKintai()
    {
        $this->KintaiErrors = [];

        // 勤怠の対象期間を設定
        $firstDate = date('Y-m-d', strtotime($this->workYear . '-' . $this->workMonth . '-01'));
        $lastDate = date('Y-m-t', strtotime($this->workYear . '-' . $this->workMonth . '-01'));

        // 勤怠がある従業員のリストを勤怠から取得
        $EmployeeIds = modelEmployeeWorks::whereBetween('wrk_date', [$firstDate, $lastDate])
            ->select('employee_id')
            ->distinct('employee_id')
            ->get();

        // 従業員ごとに勤怠をチェック
        foreach($EmployeeIds as $EmployeeId) {
            // 従業員チェック
            $Employee = employees::find($EmployeeId->employee_id);
            if(!$Employee) {
                $this->KintaiErrors[] = [
                    'empl_cd' => $EmployeeId->employee_id,
                    'empl_name' => '未登録',
                    'wrk_date' => '',
                    'message' => '従業員が見つかりません。',
                    'url' => '',
                ];
                continue;
            }
            // 勤怠を取得
            $EmployeeWorks = modelEmployeeworks::where('employee_id', $EmployeeId->employee_id)
                ->whereBetween('wrk_date', [$firstDate, $lastDate])
                ->orderByRaw('wrk_date, wrk_work_start') // 日付と勤務開始時間でソート
                ->get();

            $svWorkEnd = null;
            foreach($EmployeeWorks as $EmployeeWork) {
                // 有給ならスキップ
                if ($EmployeeWork->leave > 0) {
                    continue;
                }
                // 顧客情報を取得
                $Client = clients::find($EmployeeWork->client_id);
                if(!$Client) {
                    $this->KintaiErrors[] = [
                        'client_id' => $EmployeeWork->client_id,
                        'client_name' => '未登録',
                        'wrk_date' => '',
                        'message' => '顧客が見つかりません。',
                        'url' => '',
                    ];
                    continue;
                }
                if (empty($EmployeeWork->wrk_log_start) || empty($EmployeeWork->wrk_log_end)) {
                    $route = ($Client->cl_kintai_style == 1) ? 'employeeworksslot' : 'employeeworksone';
                    $url = route($route, [
                        'workYear' => $this->workYear, 
                        'workMonth' => $this->workMonth, 
                        'clientId' => $EmployeeWork->client_id, 
                        'clientPlaceId' => $EmployeeWork->clientplace_id, 
                        'employeeId' => $Employee->id,
                    ]);

                    $this->KintaiErrors[] = [
                        'empl_cd' => $Employee->empl_cd,
                        'empl_name' => $Employee->empl_name_last . ' ' . $Employee->empl_name_first,
                        'wrk_date' => $EmployeeWork->wrk_date,
                        'message' => '勤怠時刻が未入力です。',
                        'url' => $url,
                    ];
                }
                if($svWorkEnd && $svWorkEnd > $EmployeeWork->wrk_work_start) {
                    $route = ($this->Client->cl_kintai_style == 1) ? 'employeeworksslot' : 'employeeworksone';
                    $url = route($route, [
                        'workYear' => $this->workYear, 
                        'workMonth' => $this->workMonth, 
                        'clientId' => $EmployeeWork->client_id, 
                        'clientPlaceId' => $EmployeeWork->clientplace_id, 
                        'employeeId' => $Employee->id,
                    ]);

                    $this->KintaiErrors[] = [
                        'empl_cd' => $Employee->empl_cd,
                        'empl_name' => $Employee->empl_name_last . ' ' . $Employee->empl_name_first,
                        'wrk_date' => $EmployeeWork->wrk_date,
                        'message' => '勤怠時刻が重複しています。',
                        'url' => $url,
                    ];
                }
                $svWorkEnd = $EmployeeWork->wrk_work_end;
            }
        }
        if(count($this->KintaiErrors) > 0) {
            throw new \Exception('勤怠エラーがあります。');
        }
    }
    
    /**
     * 有給日当を検索する
     * @param integer $employee_id 従業員ID
     * @return integer 有給日当
     */
    protected function getPaidLeavePay($employee_id)
    {
        // 給与レコードを検索
        $Salary = modelSalary::where('employee_id', $employee_id)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        return $Salary ? $Salary->paid_leave_pay : 0;
    }

    /**
     * 給与レコード作成
     */
    protected function updateSalary()
    {
        $this->transport = modelEmployeeAllowDeduct::getAllowAmount($this->saveEmployeeId, $this->workYear, $this->workMonth, AppConsts::MAD_CD_TRANSPORT);    // 交通費
        $this->allowAmount = modelEmployeeAllowDeduct::getAllowTotal($this->saveEmployeeId, $this->workYear, $this->workMonth);    // 手当金額
        $this->deductAmount = modelEmployeeAllowDeduct::getDeductTotal($this->saveEmployeeId, $this->workYear, $this->workMonth);    // 控除金額

        $Salary = modelSalary::where('employee_id', $this->saveEmployeeId)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();

        if (!$Salary)
        {
            throw new \Exception('給与レコードが見つかりません。');
        }
        $Salary->working_regular_days = modelEmployeeWorks::getWorkingRegularDays($this->saveEmployeeId, $this->workYear, $this->workMonth);
        $Salary->non_statutory_days = modelEmployeeWorks::getWorkingNonStatutoryDays($this->saveEmployeeId, $this->workYear, $this->workMonth);
        $Salary->statutory_days = modelEmployeeWorks::getWorkingStatutoryDays($this->saveEmployeeId, $this->workYear, $this->workMonth);
        $Salary->paid_leave_days = modelEmployeeWorks::getPaidLeaveDays($this->saveEmployeeId, $this->workYear, $this->workMonth);

        $Salary->work_amount = $this->kintaiAmount;
        $Salary->transport = $this->transport;
        $Salary->allow_amount = $this->allowAmount;
        $Salary->deduct_amount = $this->deductAmount;
        $Salary->pay_amount = $this->kintaiAmount + $this->transport + $this->allowAmount - $this->deductAmount;
        $Salary->save();

        $this->saveEmployeeId = null;
        $this->kintaiAmount = 0;
        $this->transport = 0;
        $this->allowAmount = 0;
        $this->deductAmount = 0;
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
        $EmployeeWorks = modelEmployeeWorks::whereBetween('wrk_date', [$sStartDay, $sEndDay])
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
            // 有給の場合は支給額を再計算して保存する
            if ($EmployeeWork->leave) {
                $paid_leave_pay = $this->getPaidLeavePay($EmployeeWork->employee_id);
                $EmployeeWork->wrk_pay = $paid_leave_pay;
                $EmployeeWork->save();
            }
            // 従業員が変わった場合
            if ($this->saveEmployeeId != $EmployeeWork->employee_id) {
                // 未出力の給与レコードがあるなら、給与レコードを作成
                if($bMustWrite)
                {
                    $this->updateSalary();
                }
                $this->saveEmployeeId = $EmployeeWork->employee_id;

                $this->kintaiAmount = 0;    // 勤怠金額
                $this->transport = 0;    // 交通費
                $this->allowAmount = 0;    // 手当金額
                $this->deductAmount = 0;    // 控除金額
                $bMustWrite = false;    // 給与レコードを作成するかどうか
            }
            $this->kintaiAmount += $EmployeeWork->wrk_pay;    // 勤怠金額
            $bMustWrite = true;
        }

        // 未出力の請求明細情報があるなら、請求明細レコードを作成
        if($bMustWrite)
        {
            $this->updateSalary();
        }
    }

    /**
     * 対象従業員一覧作成
     */
    protected function createEmployeeList()
    {
        // 対象年月に勤怠がある従業員
        $dtFirstDate = strtotime($this->workYear . '-' . $this->workMonth . '-01');
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));

        try{
            $Query = modelEmployeeWorks::with('employee')
                ->select ('employeeworks.employee_id', 'employees.empl_cd')
                ->join('employees', 'employeeworks.employee_id', '=', 'employees.id')
                ->whereBetween('employeeworks.wrk_date', [date('Y-m-d', $dtFirstDate), date('Y-m-d', $dtLastDate)])
                ->orderByRaw('employees.empl_cd')
                ->distinct();
                $EmployeeWorks = $Query->get();
        } catch (\Exception $e) {
            $EmployeeWorks = [];
        }

        $Employees = [];
        foreach($EmployeeWorks as $EmployeeWork) {
            $employee_id = $EmployeeWork->employee_id;
            $salary = modelSalary::where('employee_id', $employee_id)
                ->where('work_year', $this->workYear)
                ->where('work_month', $this->workMonth)
                ->first();
            $Employees[$employee_id] = [
                'empl_cd' => $EmployeeWork->employee->empl_cd,
                'empl_name' => $EmployeeWork->employee->empl_name_last . ' ' . $EmployeeWork->employee->empl_name_first,
                'work_amount' => $salary ? $salary->work_amount : 0,
                'transport' => $salary ? $salary->transport : 0,
                'allow_amount' => $salary ? $salary->allow_amount : 0,
                'deduct_amount' => $salary ? $salary->deduct_amount : 0,
                'pay_amount' => $salary ? $salary->pay_amount : 0,
            ];
        }   

        return $Employees;
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

        // 各顧客、部門について、work_year, work_month に該当する従業員給与レコードを取得
        $Employees = $this->createEmployeeList();

        return view('livewire.closepayrolls', compact('Employees'));
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
            // 勤怠チェック
            $this->checkKintai();
            // 給与を集計
            $this->summarySalary();
            // 勤怠締めレコードを更新する
            modelClosePayrolls::closePayroll($this->workYear, $this->workMonth);
            
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
        try {
            modelClosePayrolls::openPayroll($this->workYear, $this->workMonth);

            $logMessage = '給与締め解除: ' . $this->workYear . '年' . $this->workMonth . '月';
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_PAYROLL, $logMessage);
            Session::flash('success', '解除処理が完了しました。');
        }
        catch (\Exception $e) {
            $logMessage = '給与締め解除エラー: ' . $this->workYear . '年' . $this->workMonth . '月'
                . ' ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_CLOSE_PAYROLL, $logMessage);
            session()->flash('error', '解除処理に失敗しました。');
        }
    }
}
