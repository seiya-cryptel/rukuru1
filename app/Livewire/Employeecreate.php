<?php

namespace App\Livewire;

use Livewire\Component;

use App\Traits\rukuruUtilities;

use App\Models\applogs;
use App\Models\employees as modelEmployees;

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
        $this->empl_paid_leave_pay = '';
        $this->empl_main_client_id = '';
        $this->empl_main_clientplace_id = '';
        $this->empl_notes = '';
    }

    /**
     * mount function
     */
    public function mount($id = null)
    {
        parent::mount($id);
        $this->resetFields();
        $this->updateMainClientPlaceList();
    }

    public function render()
    {
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
                'empl_paid_leave_pay' => $this->rukuruUtilMoneyValue($this->empl_paid_leave_pay),
                'empl_main_client_id' => $this->empl_main_client_id,
                'empl_main_clientplace_id' => $this->empl_main_clientplace_id,
                'empl_notes' => $this->empl_notes,
            ]);
            $logMessage = '従業員 作成: ' . $this->empl_cd . ' ' . $this->empl_name_last . ' ' . $this->empl_name_first;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_EMPLOYEE, $logMessage);
            session()->flash('success', __('Employee created successfully.'));
            return redirect()->route('employee');
        } catch (\Exception $e) {
            $logMessage = '従業員 作成 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
