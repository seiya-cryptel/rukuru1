<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;
use App\Models\applogs;
use App\Models\employees as modelEmployees;

class Employees extends Component
{
    use WithPagination;

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
        $this->search = session(AppConsts::SESS_SEARCH, '');
        $this->retire = session(AppConsts::SESS_RETIRE, false);
    }

    public function render()
    {
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
                    ->orWhere('empl_main_client_name', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_notes', 'like', '%'.$this->search.'%')
                    ->orWhere('empl_cd', 'like', '%'.$this->search.'%');
            });
        }
        $Employees = $Query->paginate(AppConsts::PAGINATION);
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
            modelEmployees::destroy($id);
            $logMessage = '従業員 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_EMPLOYEE, $logMessage);
            session()->flash('success', __('Employee deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '従業員 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * change search keyword
     */
    public function changeSearch()
    {
        session([AppConsts::SESS_SEARCH => $this->search]);
    }

    /**
     * clear search keyword
     */
    public function clearSearch()
    {
        $this->search = '';
        session([AppConsts::SESS_SEARCH => '']);
    }

    /**
     * change retire flag
     */
    public function changeRetire()
    {
        session([AppConsts::SESS_RETIRE => $this->retire]);
    }
}
