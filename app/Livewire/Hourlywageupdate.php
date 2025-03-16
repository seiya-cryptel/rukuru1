<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\applogs;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorkTypes;
use App\Models\employeepays as modelEmployeePays;

class Hourlywageupdate extends HourlywageBase
{
    /**
     * mount function
     * @param int $employeepay_id   employee pay id
     */
    public function mount($employee_id, $employeepay_id = null)
    {
        $employeePay = modelEmployeePays::find($employeepay_id);
        if (empty($employeePay)) {
            session()->flash('error', __('Hourly Wage') . ' ' . __('Not Found'));
            return redirect()->route('employee');
        }

        parent::mount($employee_id, $employeepay_id);

        $this->employee_id = $employeePay->employee_id;
        $this->clientworktype_id = $employeePay->clientworktype_id;
        $this->wt_pay_std = $employeePay->wt_pay_std;
        $this->wt_pay_ovr = $employeePay->wt_pay_ovr;
        $this->wt_pay_ovr_midnight = $employeePay->wt_pay_ovr_midnight;
        $this->wt_pay_holiday = $employeePay->wt_pay_holiday;
        $this->wt_pay_holiday_midnight = $employeePay->wt_pay_holiday_midnight;
        $this->wt_bill_std = $employeePay->wt_bill_std;
        $this->wt_bill_ovr = $employeePay->wt_bill_ovr;
        $this->wt_bill_ovr_midnight = $employeePay->wt_bill_ovr_midnight;
        $this->wt_bill_holiday = $employeePay->wt_bill_holiday;
        $this->wt_bill_holiday_midnight = $employeePay->wt_bill_holiday_midnight;
        $this->notes = $employeePay->notes;

        $ClientWorkType = modelClientWorkTypes::find($employeePay->clientworktype_id);
        $this->client_id = $ClientWorkType->client_id;
        $this->clientplace_id = $ClientWorkType->clientplace_id;
        // 作業種別リストを更新する
        $this->updateClientWorkTypeList();

        $this->clientworktype_id = $ClientWorkType->id;
        // 作業種別情報を更新する
        $this->updateClientWorkTypeId();
    }

    public function render()
    {
        return view('livewire.hourlywageupdate');
    }

    /**
     * update function
     */
    public function updateEmployeePay()
    {
        $this->validate();
        try {
            $employeePay = modelEmployeePays::find($this->employeepay_id);
            $employeePay->clientworktype_id = $this->clientworktype_id;
            $employeePay->wt_pay_std = $this->str2decimal($this->wt_pay_std);
            $employeePay->wt_pay_ovr = $this->str2decimal($this->wt_pay_ovr);
            $employeePay->wt_pay_ovr_midnight = $this->str2decimal($this->wt_pay_ovr_midnight);
            $employeePay->wt_pay_holiday = $this->str2decimal($this->wt_pay_holiday);
            $employeePay->wt_pay_holiday_midnight = $this->str2decimal($this->wt_pay_holiday_midnight);
            $employeePay->wt_bill_std = $this->str2decimal($this->wt_bill_std);
            $employeePay->wt_bill_ovr = $this->str2decimal($this->wt_bill_ovr);
            $employeePay->wt_bill_ovr_midnight = $this->str2decimal($this->wt_bill_ovr_midnight);
            $employeePay->wt_bill_holiday = $this->str2decimal($this->wt_bill_holiday);
            $employeePay->wt_bill_holiday_midnight = $this->str2decimal($this->wt_bill_holiday_midnight);
            $employeePay->notes = $this->notes;
            $employeePay->save();

            $logMessage = '従業員時給 更新: ' . $this->Employee->empl_cd . ' ' . $employeePay->id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_EMPLOYEEPAY, $logMessage);
            session()->flash('success', __('Employee Pay updated successfully.'));
            return redirect()->route('hourlywage', ['id' => $this->employee_id]);
        } catch (\Exception $e) {
            $logMessage = '従業員時給 更新 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
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
