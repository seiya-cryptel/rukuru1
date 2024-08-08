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
     * record set of master allow deducts
     * */
    // public $Employees;
    /**
     * master allow deducts fields
     */
    public $empl_cd, 
        $empl_name_last, $empl_name_middle, $empl_name_first,
        $empl_kana_last, $empl_kana_middle, $empl_kana_first,
        $empl_alpha_last, $empl_alpha_middle, $empl_alpha_first,
        $empl_sex,
        $empl_email, $empl_mobile,
        $empl_hire_date, $empl_resign_date,
        $empl_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $employeeId, $updateEmployee = false, $addEmployee = false, $hourlyWageEmployee = false;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deletEmployeeListener' => 'deleteEmployee',
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'empl_cd' => 'required',
        'empl_name_last' => 'required',
        'empl_name_first' => 'required',
        'empl_kana_last' => 'required',
        'empl_kana_first' => 'required',
        'empl_alpha_last' => 'required',
        'empl_alpha_first' => 'required',
    ];

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->empl_cd = '';
        $this->empl_name_last = '';
        $this->empl_name_middle = '';
        $this->empl_name_first = '';
        $this->empl_kana_last = '';
        $this->empl_kana_middle = '';
        $this->empl_kana_first = '';
        $this->empl_alpha_last = '';
        $this->empl_alpha_middle = '';
        $this->empl_alpha_first = '';
        $this->empl_sex = '';
        $this->empl_email = '';
        $this->empl_mobile = '';
        $this->empl_hire_date = '';
        $this->empl_resign_date = '';
        $this->empl_notes = '';
    }

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
        $this->resetFields();
        $this->addEmployee = true;
        $this->updateEmployee = false;
        $this->hourlyWageEmployee = false;
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeEmployee()
    {
        $this->validate();
        try {
            modelEmployees::create([
                'empl_cd' => $this->empl_cd,
                'empl_name_last' => $this->empl_name_last,
                'empl_name_middle' => $this->empl_name_middle,
                'empl_name_first' => $this->empl_name_first,
                'empl_kana_last' => $this->empl_kana_last,
                'empl_kana_middle' => $this->empl_kana_middle,
                'empl_kana_first' => $this->empl_kana_first,
                'empl_alpha_last' => $this->empl_alpha_last,
                'empl_alpha_middle' => $this->empl_alpha_middle,
                'empl_alpha_first' => $this->empl_alpha_first,
                'empl_sex' => $this->empl_sex,
                'empl_email' => $this->empl_email,
                'empl_mobile' => $this->empl_mobile,
                'empl_hire_date' => $this->empl_hire_date,
                'empl_resign_date' => $this->empl_resign_date,
                'empl_notes' => $this->empl_notes,
            ]);
            $this->resetFields();
            $this->addEmployee = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editEmployee($id) {
        try {
            $Employee = modelEmployees::findOrFail($id);
            if(!$Employee) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->employeeId = $id;
                $this->empl_cd = $Employee->empl_cd;
                $this->empl_name_last = $Employee->empl_name_last;
                $this->empl_name_middle = $Employee->empl_name_middle;
                $this->empl_name_first = $Employee->empl_name_first;
                $this->empl_kana_last = $Employee->empl_kana_last;
                $this->empl_kana_middle = $Employee->empl_kana_middle;
                $this->empl_kana_first = $Employee->empl_kana_first;
                $this->empl_alpha_last = $Employee->empl_alpha_last;
                $this->empl_alpha_middle = $Employee->empl_alpha_middle;
                $this->empl_alpha_first = $Employee->empl_alpha_first;
                $this->empl_sex = $Employee->empl_sex;
                $this->empl_email = $Employee->empl_email;
                $this->empl_mobile = $Employee->empl_mobile;
                $this->empl_hire_date = $Employee->empl_hire_date;
                $this->empl_resign_date = $Employee->empl_resign_date;
                $this->empl_notes = $Employee->empl_notes;
                
                $this->updateEmployee = true;
                $this->addEmployee = false;
                $this->hourlyWageEmployee = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updateEmployee() {
        $this->validate();
        try {
            modelEmployees::where('id', $this->employeeId)->update([
                'empl_cd' => $this->empl_cd,
                'empl_name_last' => $this->empl_name_last,
                'empl_name_middle' => $this->empl_name_middle,
                'empl_name_first' => $this->empl_name_first,
                'empl_kana_last' => $this->empl_kana_last,
                'empl_kana_middle' => $this->empl_kana_middle,
                'empl_kana_first' => $this->empl_kana_first,
                'empl_alpha_last' => $this->empl_alpha_last,
                'empl_alpha_middle' => $this->empl_alpha_middle,
                'empl_alpha_first' => $this->empl_alpha_first,
                'empl_sex' => $this->empl_sex,
                'empl_email' => $this->empl_email,
                'empl_mobile' => $this->empl_mobile,
                'empl_hire_date' => $this->empl_hire_date,
                'empl_resign_date' => $this->empl_resign_date,
                'empl_notes' => $this->empl_notes,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployee() {
        $this->resetFields();
        $this->addEmployee = false;
        $this->updateEmployee = false;
        $this->hourlyWageEmployee = false;
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

    /**
     * Open Hourly Wage form
     * @return void
     */
    public function hourlywageEmployee($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->addEmployee = false;
        $this->updateEmployee = false;
        $this->hourlyWageEmployee = true;
    }
}
