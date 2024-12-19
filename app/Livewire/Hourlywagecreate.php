<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\HourlywageBase;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorkTypes;
use App\Models\employeepays as modelEmployeePays;

class Hourlywagecreate extends HourlywageBase
{

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->clientworktype_id = '';
        $this->wt_pay_std = '';
        $this->wt_pay_ovr = '';
        $this->wt_pay_ovr_midnight = '';
        $this->wt_pay_holiday = '';
        $this->wt_pay_holiday_midnight = '';
        $this->wt_bill_std = '';
        $this->wt_bill_ovr = '';
        $this->wt_bill_ovr_midnight = '';
        $this->wt_bill_holiday = '';
        $this->wt_bill_holiday_midnight = '';
        $this->wt_notes = '';
    }

    /**
     * mount function
     * @param int $employee_id   employee id
     */
    public function mount($employee_id)
    {
        parent::mount($employee_id);

        $this->resetFields();
    }

    public function render()
    {
        return view('livewire.hourlywagecreate');
    }

    /**
     * Save Employee Pay
     */
    public function saveEmployeePay()
    {
        $this->validate();
        try {
            $EmployeePay = new modelEmployeePays();
            $EmployeePay->employee_id = $this->employee_id;
            $EmployeePay->clientworktype_id = $this->clientworktype_id;
            $EmployeePay->wt_pay_std = self::str2decimal($this->wt_pay_std);
            $EmployeePay->wt_pay_ovr = self::str2decimal($this->wt_pay_ovr);
            $EmployeePay->wt_pay_ovr_midnight = self::str2decimal($this->wt_pay_ovr_midnight);
            $EmployeePay->wt_pay_holiday = self::str2decimal($this->wt_pay_holiday);
            $EmployeePay->wt_pay_holiday_midnight = self::str2decimal($this->wt_pay_holiday_midnight);
            $EmployeePay->wt_bill_std = self::str2decimal($this->wt_bill_std);
            $EmployeePay->wt_bill_ovr = self::str2decimal($this->wt_bill_ovr);
            $EmployeePay->wt_bill_ovr_midnight = self::str2decimal($this->wt_bill_ovr_midnight);
            $EmployeePay->wt_bill_holiday = self::str2decimal($this->wt_bill_holiday);
            $EmployeePay->wt_bill_holiday_midnight = self::str2decimal($this->wt_bill_holiday_midnight);
            $EmployeePay->notes = $this->wt_notes;
            $EmployeePay->save();

            session()->flash('success', __('Create'). ' ' . __('Done'));
            return redirect()->route('hourlywage', ['id' => $this->employee_id]);
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployeePay() {
        return redirect()->route('hourlywage', ['id' => $this->employee_id]);
    }
}
