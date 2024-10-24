<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\employees as modelEmployees;

class Employees extends Component
{
    use WithPagination;

    public $search = '';

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteEmployeeListener' => 'deleteEmployee',
    ];

    public function render()
    {
        $Employees = modelEmployees::where('empl_name_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_name_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_email', 'like', '%'.$this->search.'%')
            ->orWhere('empl_mobile', 'like', '%'.$this->search.'%')
            ->orWhere('empl_cd', 'like', '%'.$this->search.'%')
            ->paginate(10);
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
