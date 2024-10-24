<?php

namespace App\Livewire;
use App\Models\employees as modelEmployees;

use Livewire\Component;

class Employeeupdate extends Component
{
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
     * mount the component
     */
    public function mount($id)
    {
        $Employee = modelEmployees::find($id);
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
        $this->empl_sex = $Employee->sex;
        $this->empl_email = $Employee->empl_email;
        $this->empl_mobile = $Employee->empl_mobile;
        $this->empl_hire_date = $Employee->empl_hire_date;
        $this->empl_resign_date = $Employee->empl_resign_date;
        $this->empl_notes = $Employee->empl_notes;
    }

    public function render()
    {
        return view('livewire.employeeupdate');
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
        return redirect()->route('employee');
    }
}
