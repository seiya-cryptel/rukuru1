<?php

namespace App\Livewire;
use App\Models\employees as modelEmployees;

use Livewire\Component;

use App\Traits\rukuruUtilities;

class Employeecreate extends EmployeeBase
{
    use rukuruUtilities;

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
        $this->empl_main_client_name = '';
        $this->empl_notes = '';
    }

    /**
     * mount function
     */
    public function mount($id = null)
    {
        parent::mount($id);

        $this->resetFields();
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
                'empl_main_client_name' => $this->empl_main_client_name,
                'empl_notes' => $this->empl_notes,
            ]);
            session()->flash('success', __('Create'). ' ' . __('Done'));
            return redirect()->route('employee');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
