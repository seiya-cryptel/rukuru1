<?php

namespace App\Livewire;

use Livewire\Component;
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
        return redirect()->route('hourlywagecreate', ['id' => $this->employee_id]);
    }

    /**
     * Open Edit Employee Pay form
     */
    public function editEmployeepay($id)
    {
        return redirect()->route('hourlywageupdate', ['id' => $id]);
    }

    /**
     * delete Employee Pay
     */
    public function deleteEmployeepay($id)
    {
        modelEmployeePay::find($id)->delete();
    }
}
