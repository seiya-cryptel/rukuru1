<?php

namespace App\Livewire;
use App\Models\employees as modelEmployees;

use Livewire\Component;

use App\Models\clients as modelClients;

// use App\Traits\rukuruUtilities;

abstract class EmployeeBase extends Component
{
    // use rukuruUtilities;

    /**
     * record set of table clients and client places
     * */
    public $refClients;

    /**
     * fields
     */
    public $empl_cd, 
        $empl_name_last, $empl_name_middle, $empl_name_first,
        $empl_kana_last, $empl_kana_middle, $empl_kana_first,
        $empl_alpha_last, $empl_alpha_middle, $empl_alpha_first,
        $empl_sex,
        $empl_email, $empl_mobile,
        $empl_hire_date, $empl_resign_date,
        $empl_main_client_name,
        $empl_notes;

    /**
     * id value
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
     * mount function
     */
    public function mount($id = null)
    {
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
    }

    abstract public function render();

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployee() {
        return redirect()->route('employee');
    }
}
