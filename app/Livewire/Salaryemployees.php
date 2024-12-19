<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\employees as modelEmployees;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;

class Salaryemployees extends Component
{
    use WithPagination;

    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * search string for employees
     */
    public $search = '';

    /**
     * rules for validation
     */
    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

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

        // 従業員検索条件を取得
        if(session()->has(AppConsts::SESS_SEARCH)) {
            $this->search = session(AppConsts::SESS_SEARCH);
        } else {
            $this->search = '';
        }
    }

    public function render()
    {
        // 勤怠対象月の初日と最終日を取得
        $firstDay = date('Y-m-01', strtotime($this->workYear.'-'.$this->workMonth.'-01'));
        $lastDay = date('Y-m-t', strtotime($this->workYear.'-'.$this->workMonth.'-01'));

        // 氏名やコードの一部で検索
        $query = modelEmployees::where(function ($query) {
            $query->where('empl_name_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_name_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_email', 'like', '%'.$this->search.'%')
            ->orWhere('empl_mobile', 'like', '%'.$this->search.'%')
            ->orWhere('empl_cd', 'like', '%'.$this->search.'%')
            ;
        });

        $query = $query->where('empl_hire_date', '<=', $lastDay);

        $query = $query->where(function ($query) use ($firstDay) {
            $query->where('empl_resign_date', '>=', $firstDay)
            ->orWhere('empl_resign_date', null)
            ->orWhere('empl_resign_date', '0000-00-00 00:00:00')
            ->orWhere('empl_resign_date', '');
        });

        $Employees = $query->paginate(AppConsts::PAGINATION);

        return view('livewire.salaryemployees', compact('Employees'));
    }

    /**
     * clear search string
     * */
    public function clearSearch()
    {
        $this->search = '';
        session([AppConsts::SESS_SEARCH => $this->search]);
    }

    /**
     * work year updated
     */
    public function updateWorkYear($value)
    {
        session([AppConsts::SESS_WORK_YEAR => $value]);
    }

    /**
     * work month updated
     */
    public function updateWorkMonth($value)
    {
        session([AppConsts::SESS_WORK_YEAR => $value]);
    }

    /**
     * redirect to allow/deduct edit page 
     * */
    public function editSalary($employeeId)
    {
        // 戻る画面を手当控除入力従業員一覧に設定
        session()->forget(AppConsts::SESS_PREVIOUS_URL);

        return redirect()->route('employeesalary', 
            ['workYear' => $this->workYear, 'workMonth' => $this->workMonth, 'employeeId' => $employeeId]);
    }

    /**
     * allow deduct record exists?
     * */
    public function allowDeductExists($employeeId)
    {
        try {
            $this->validate();
        } catch (\Exception $e) {
            return 'error';
        }

        // 勤怠データが存在するかどうかを確認する
        $firstDay = date('Y-m-01', strtotime($this->workYear.'-'.$this->workMonth.'-01'));
        $lastDay = date('Y-m-t', strtotime($this->workYear.'-'.$this->workMonth.'-01'));

        $Query = modelEmployeeAllowDeduct::where('employee_id', $employeeId)
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth);
        return $Query->exists() ? 'exists' : 'notexists';
    }
}
