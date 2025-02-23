<?php

namespace App\Livewire;
use Livewire\Component;

use App\Traits\rukuruUtilities;

use App\Models\applogs;
use App\Models\employees as modelEmployees;

class Employeeupdate extends EmployeeBase
{
    use rukuruUtilities;

    /**
     * mount the component
     */
    public function mount($id = null)
    {
        parent::mount($id);

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
        $this->empl_paid_leave_pay = $Employee->empl_paid_leave_pay;
        $this->empl_main_client_id = $Employee->empl_main_client_id;
        $this->empl_main_clientplace_id = $Employee->empl_main_clientplace_id;
        $this->empl_notes = $Employee->empl_notes;
        
        $this->updateMainClientPlaceList();
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
                'empl_hire_date' => $this->rukuruUtilEmptyToNull($this->empl_hire_date),
                'empl_resign_date' => $this->rukuruUtilEmptyToNull($this->empl_resign_date),
                'empl_paid_leave_pay' => $this->rukuruUtilMoneyValue($this->empl_paid_leave_pay),
                'empl_main_client_id' => $this->empl_main_client_id,
                'empl_main_clientplace_id' => $this->empl_main_clientplace_id,
                'empl_notes' => $this->empl_notes,
            ]);
            $logMessage = '従業員 更新: ' . $this->empl_cd . ' ' . $this->empl_name_last . ' ' . $this->empl_name_first;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_EMPLOYEE, $logMessage);
            session()->flash('success', __('Employee updated successfully.'));
            return redirect()->route('employee');
        } catch (\Exception $e) {
            $logMessage = '従業員 更新 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
