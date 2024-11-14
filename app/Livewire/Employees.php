<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\employees as modelEmployees;

class Employees extends Component
{
    use WithPagination;

    /**
     * session variable key
     */
    public const __CLASS__ = 'Employees';
    public const SESS_SEARCH = self::__CLASS__ . '_search';
    public const SESS_RETIRE = self::__CLASS__ . '_retire';

    /**
     * search keyword
     */
    public $search = '';

    /**
     * 退職者表示フラグ
     */
    public bool $retire = false;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteEmployeeListener' => 'deleteEmployee',
    ];

    /**
     * mount function
     */
    public function mount()
    {
        $this->search = session(self::SESS_SEARCH, '');
        $this->retire = session(self::SESS_RETIRE, false);
    }

    public function render()
    {
        // 検索条件をセッションに保存
        session([self::SESS_SEARCH => $this->search]);
        session([self::SESS_RETIRE => $this->retire]);

        $Query = modelEmployees::query();
        // 退職者非表示
        if (! $this->retire) {
            $Query->whereNull('empl_resign_date')
            ->orWhere('empl_resign_date', '=', '0000-00-00');
        }
        // 文字列検索
        if(! empty($this->search)) {
            $Query->where(function($query) {
                $query->where('empl_name_last', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_name_first', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_kana_last', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_kana_first', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_alpha_last', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_alpha_first', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_email', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_mobile', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_notes', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_cd', 'like', '%'.$this->search.'%');
            });
        }
        $Employees = $Query->paginate(10);
        return view('livewire.employees', compact('Employees'));
    }

    /**
     * Open Add Employee form
     * @return void
     */
    public function newEmployee()
    {
        return redirect()->route('employeecreate');
    }

    /**
     * Open Edit Employee form
     * @return void
     */
    public function editEmployee($id)
    {
        return redirect()->route('employeeupdate', ['id' => $id]);
    }

    /**
     * Open Hourly Wage form
     * @return void
     */
    public function hourlywageEmployee($id)
    {
        return redirect()->route('hourlywage', ['id' => $id]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteEmployee($id) {
        try {
            modelEmployees::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
