<?php

namespace App\Livewire;
use App\Models\employees as modelEmployees;

use Livewire\Component;

use App\Traits\rukuruUtilites;

class Employeecreate extends Component
{
    use rukuruUtilites;

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
    public $employeeId;

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
        $this->resetFields();
        return view('livewire.employeecreate');
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
                'empl_hire_date' => $this->rukuruUtilEmptyToNull($this->empl_hire_date),
                'empl_resign_date' => $this->rukuruUtilEmptyToNull($this->empl_resign_date),
                'empl_notes' => $this->empl_notes,
            ]);
            return redirect()->route('employee');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployee() {
        return redirect()->route('employee');
    }
}
