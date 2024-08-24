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
        if (session()->has('workYear')) {
            $this->workYear = session('workYear');
            session()->forget('workYear');
        } else {
            $this->workYear = date('Y');
        }
        if(session()->has('workMonth')) {
            $this->workMonth = session('workMonth');
            session()->forget('workMonth');
        } else {
            $this->workMonth = date('m');
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

        /*
        $query->whereExists(function ($query) use ($firstDay, $lastDay) {
            $query->select(DB::raw(1))
                  ->from('employeeworks')
                  ->whereColumn('employeeworks.employee_id', 'employees.id')
                  ->whereBetween('employeeworks.wrk_date', [$firstDay, $lastDay]);
        });
        */

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
     * redirect to allow/deduct edit page 
     * */
    public function editSalary($employeeId)
    {
        // セッション変数にキー（employeeId、workYear、workMonth、client_id、clientplace_id）を設定
        session(['employee_id' => $employeeId]);
        session(['workYear' => $this->workYear]);
        session(['workMonth' => $this->workMonth]);
        return redirect()->route('employeesalary');
    }
}
