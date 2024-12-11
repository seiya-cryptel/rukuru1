<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;

use Livewire\WithPagination;
use Livewire\Component;

use App\Models\employees as modelEmployees;

class Salaryemployees extends Component
{
    use WithPagination;

    /**
     * session variable key
     */
    public const __CLASS__ = 'Salaryemployees';
    public const SESS_WORKYEAR = self::__CLASS__ . '_workYear';
    public const SESS_WORKMONTH = self::__CLASS__ . '_workMonth';
    public const SESS_SEARCH = self::__CLASS__ . '_search';

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
        // set default values
        // 対象年月を設定
        // セッション変数にキー（workYear、workMonth）が設定されている場合は、その値を取得
        // 値を取得したあとは、セッション変数を削除
        if (session()->has(self::SESS_WORKYEAR)) {
            $this->workYear = session(self::SESS_WORKYEAR);
        } else {
            $this->workYear = date('Y');
            session([self::SESS_WORKYEAR => $this->workYear]);
        }
        if(session()->has('workMonth')) {
            $this->workMonth = session('workMonth');
            session()->forget('workMonth');
        } else {
            $this->workMonth = date('m');
        }

        if(session()->has(self::SESS_WORKMONTH)) {
            $this->workMonth = session(self::SESS_WORKMONTH);
        } else {
            $this->workMonth = date('m');
            $Day = date('d');
            if ($Day < 15) {
                $this->workYear = date('Y', strtotime('-1 month'));
                $this->workMonth = date('m', strtotime('-1 month'));
            }
            session([self::SESS_WORKYEAR => $this->workYear]);
            session([self::SESS_WORKMONTH => $this->workMonth]);
        }

        // 従業員検索条件を取得
        if(session()->has(self::SESS_SEARCH)) {
            $this->search = session(self::SESS_SEARCH);
        } else {
            $this->search = '';
        }
    }

    public function render()
    {
        // 勤怠対象月の初日と最終日を取得
        $firstDay = date('Y-m-01', strtotime($this->workYear.'-'.$this->workMonth.'-01'));
        $lastDay = date('Y-m-t', strtotime($this->workYear.'-'.$this->workMonth.'-01'));

        // 従業員検索条件をセッションに保存
        session([self::SESS_SEARCH => $this->search]);

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

        $Employees = $query->paginate(10);

        return view('livewire.salaryemployees', compact('Employees'));
    }

    /**
     * clear search string
     * */
    public function clearSearch()
    {
        $this->search = '';
    }

    /**
     * work year updated
     */
    public function updateWorkYear($value)
    {
        session([self::SESS_WORKYEAR => $value]);
    }

    /**
     * work month updated
     */
    public function updateWorkMonth($value)
    {
        session([self::SESS_WORKMONTH => $value]);
    }

    /**
     * redirect to allow/deduct edit page 
     * */
    public function editSalary($employeeId)
    {
        return redirect()->route('employeesalary', 
            ['workYear' => $this->workYear, 'workMonth' => $this->workMonth, 'employeeId' => $employeeId]);
    }
}
