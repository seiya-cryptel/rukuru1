<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\applogs;
use App\Models\Employees as modelEmployees;
use App\Models\employeepays as modelEmployeePays;

/**
 * Hourlywage class
 * 従業員の時給一覧
 */
class Hourlywage extends Component
{
    /**
     * Employee record
     */
    public $Employee;

    public $employee_id;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteEmployeePayListener' => 'deleteEmployeePay',
    ];

    /**
     * mount function
     * param int $employee_id   employee id
     */
    public function mount($employee_id)
    {
        $this->employee_id = $employee_id;
        $this->Employee = modelEmployees::find($employee_id);
    }

    public function render()
    {
        $EmployeePays = modelEmployeePays::with('Employee')
            ->with('ClientWorkType')
            ->with('ClientWorkType.Client')
            ->with('ClientWorkType.ClientPlace')
            ->where('employee_id', $this->employee_id)
            ->get();
        return view('livewire.hourlywage', compact('EmployeePays'));
    }

    /**
     * Open Add Employee Pay form
     */
    public function newEmployeepay()
    {
        return redirect()->route('hourlywagecreate', ['employee_id' => $this->employee_id]);
    }

    /**
     * Open Edit Employee Pay form
     */
    public function editEmployeePay($id)
    {
        return redirect()->route('hourlywageupdate', ['employee_id' => $this->employee_id, 'employeepay_id' => $id]);
    }

    /**
     * delete Employee Pay
     */
    public function deleteEmployeePay($id)
    {
        try {modelEmployeePays::destroy($id);
            $logMessage = '従業員時給 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_EMPLOYEEPAY, $logMessage);
            session()->flash('success', __('Employee Pay deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '従業員時給 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
