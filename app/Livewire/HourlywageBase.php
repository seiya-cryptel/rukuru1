<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorkTypes;
use App\Models\employees as modelEmployee;
use App\Models\employeepays as modelEmployeePays;

abstract class HourlywageBase extends Component
{
    /**
     * 金額項目の背景色定数
     */
    const WAGE_COLOR_DIFF = 'text-red-500';
    const WAGE_COLOR_SAME = '';

    /**
     * client, clientworktype record
     */
    public $refClients;
    public $refClientPlaces = [];
    public $refClientWorkTypes;

    /**
     * clientworktype record
     */
    public $Employee;
    public $ClientWorkType;

    /**
     * client, clientplace, clientworktype id
     */
    public $client_id = "";
    public $clientplace_id = "";

    /**
     * clientworktype attributes
     */
    public $wt_name = "";
    public $wt_kana = "";
    public $wt_alpha = "";
    public $wt_day_night_name = "";
    public $wt_work_start = "";
    public $wt_work_end = "";
    public $wt_lunch_break = "";
    public $wt_evening_break = "";
    public $wt_night_break = "";
    public $wt_midnight_break = "";

    /**
     * fields
     */
    public $employeepay_id, $employee_id, $clientworktype_id,
        $wt_pay_std, $wt_pay_ovr, $wt_pay_ovr_midnight, $wt_pay_holiday, $wt_pay_holiday_midnight,
        $wt_bill_std, $wt_bill_ovr, $wt_bill_ovr_midnight, $wt_bill_holiday, $wt_bill_holiday_midnight,
        $notes;

    /**
     * 金額項目の背景色
     */
    public $bg_wt_pay_std = "";
    public $bg_wt_pay_ovr = "";
    public $bg_wt_pay_ovr_midnight = "";
    public $bg_wt_pay_holiday = "";
    public $bg_wt_pay_holiday_midnight = "";
    public $bg_wt_bill_std = "";
    public $bg_wt_bill_ovr = "";
    public $bg_wt_bill_ovr_midnight = "";
    public $bg_wt_bill_holiday = "";
    public $bg_wt_bill_holiday_midnight = "";

    /**
     * 金額表示文字列の数値化
     * mutator is not working in livewire
     */
    protected static function str2decimal($str) {
        return (double)str_replace(',', '', $str);
    }

    /**
     * 金額表示背景色の設定
     */
    protected function setWageBGColor($fieldName)
    {  
        $val = ($this->ClientWorkType && ($this->str2decimal($this->ClientWorkType->$fieldName) != $this->str2decimal($this->$fieldName)))
         ? self::WAGE_COLOR_DIFF : self::WAGE_COLOR_SAME;
        $bg_fieldName = 'bg_' . $fieldName;
        $this->$bg_fieldName = $val;
    }

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'employee_id' => 'required',
        'client_id' => 'required',
        'clientworktype_id' => 'required',
    ];

    /**
     * List of add/edit form validation error messages
     */
    protected $messages = [
        'employee_id.required' => "必須",
        'client_id.required' => "必須",
        'clientworktype_id.required' => "必須",
    ];

    /**
     * mount function
     * @param int $employeepay_id  employeepay id
     */
    public function mount($employee_id, $employeepay_id = null)
    {
        $this->employeepay_id = $employeepay_id;
        $this->Employee = modelEmployee::find($employee_id);
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
        $this->refClientPlaces = modelClientPlaces::orderBy('cl_pl_name', 'asc')->get();
        $this->updateClientWorkTypeList();
    }

    abstract public function render();

    /**
     * renew client work type list
     */
    protected function updateClientWorkTypeList()
    {
        $Query = modelClientWorkTypes::query();
        if (empty($this->client_id)) {
            $Query->whereNull('client_id');
        } else {
            $Query->where('client_id', $this->client_id);
        }
        if (empty($this->clientplace_id)) {
            $Query->whereNull('clientplace_id');
        } else {
            $Query->where('clientplace_id', $this->clientplace_id);
        }
        $this->refClientWorkTypes = $Query->orderBy('wt_cd', 'asc')->get();
    }

    /**
     * client_id change event
     */
    public function updateClientId()
    {
        $this->clientplace_id = '';
        $this->refClientPlaces = modelClientPlaces::where('client_id', $this->client_id)
            ->orderBy('cl_pl_cd', 'asc')
            ->get();
        $this->updateClientWorkTypeList();
    }

    /**
     * clientplace_id change event
     */
    public function updateClientPlaceId()
    {
        $this->updateClientWorkTypeList();
    }

    /**
     * wage item value change event
     */
    public function updateWage($val, $fieldName)
    {
        $numVal = abs(intval($this->str2decimal($val)));
        $this->$fieldName = number_format($numVal);
        $this->setWageBGColor($fieldName);
    }

    /**
     * clientworktype_id change event
     * load clientworktype data
     */
    public function updateClientWorkTypeId()
    {
        $this->ClientWorkType = modelClientWorkTypes::find($this->clientworktype_id);
        if (empty($this->ClientWorkType)) {
            $this->wt_name = "";
            $this->wt_kana = "";
            $this->wt_alpha = "";
            $this->wt_day_night_name = "";
            $this->wt_work_start = "";
            $this->wt_work_end = "";
            $this->wt_lunch_break = "";
            $this->wt_evening_break = "";
            $this->wt_night_break = "";
            $this->wt_midnight_break = "";
    
            /* don't need to load pay/bill data
            $this->wt_pay_std = $this->ClientWorkType->wt_pay_std;
            $this->wt_pay_ovr = $this->ClientWorkType->wt_pay_ovr;
            $this->wt_pay_ovr_midnight = $this->ClientWorkType->wt_pay_ovr_midnight;
            $this->wt_pay_holiday = $this->ClientWorkType->wt_pay_holiday;
            $this->wt_pay_holiday_midnight = $this->ClientWorkType->wt_pay_holiday_midnight;
            $this->wt_bill_std = $this->ClientWorkType->wt_bill_std;
            $this->wt_bill_ovr = $this->ClientWorkType->wt_bill_ovr;
            $this->wt_bill_ovr_midnight = $this->ClientWorkType->wt_bill_ovr_midnight;
            $this->wt_bill_holiday = $this->ClientWorkType->wt_bill_holiday;
            $this->wt_bill_holiday_midnight = $this->ClientWorkType->wt_bill_holiday_midnight;
            */
        }
        else {
            $this->wt_name = $this->ClientWorkType->wt_name;
            $this->wt_kana = $this->ClientWorkType->wt_kana;
            $this->wt_alpha = $this->ClientWorkType->wt_alpha;
            $this->wt_day_night_name = $this->ClientWorkType->wt_day_night == 1 ? __('Day Work') : __('Night Work');
            $this->wt_work_start = $this->ClientWorkType->wt_work_start;
            $this->wt_work_end = $this->ClientWorkType->wt_work_end;
            $this->wt_lunch_break = $this->ClientWorkType->wt_lunch_break;
            $this->wt_evening_break = $this->ClientWorkType->wt_evening_break;
            $this->wt_night_break = $this->ClientWorkType->wt_night_break;
            $this->wt_midnight_break = $this->ClientWorkType->wt_midnight_break;
    
            if(empty($this->wt_pay_std)) $this->wt_pay_std = $this->ClientWorkType->wt_pay_std;
            if(empty($this->wt_pay_ovr)) $this->wt_pay_ovr = $this->ClientWorkType->wt_pay_ovr;
            if(empty($this->wt_pay_ovr_midnight)) $this->wt_pay_ovr_midnight = $this->ClientWorkType->wt_pay_ovr_midnight;
            if(empty($this->wt_pay_holiday)) $this->wt_pay_holiday = $this->ClientWorkType->wt_pay_holiday;
            if(empty($this->wt_pay_holiday_midnight)) $this->wt_pay_holiday_midnight = $this->ClientWorkType->wt_pay_holiday_midnight;
            if(empty($this->wt_bill_std)) $this->wt_bill_std = $this->ClientWorkType->wt_bill_std;
            if(empty($this->wt_bill_ovr)) $this->wt_bill_ovr = $this->ClientWorkType->wt_bill_ovr;
            if(empty($this->wt_bill_ovr_midnight)) $this->wt_bill_ovr_midnight = $this->ClientWorkType->wt_bill_ovr_midnight;
            if(empty($this->wt_bill_holiday)) $this->wt_bill_holiday = $this->ClientWorkType->wt_bill_holiday;
            if(empty($this->wt_bill_holiday_midnight)) $this->wt_bill_holiday_midnight = $this->ClientWorkType->wt_bill_holiday_midnight;

            $this->setWageBGColor('wt_pay_std');
            $this->setWageBGColor('wt_pay_ovr');
            $this->setWageBGColor('wt_pay_ovr_midnight');
            $this->setWageBGColor('wt_pay_holiday');
            $this->setWageBGColor('wt_pay_holiday_midnight');
            $this->setWageBGColor('wt_bill_std');
            $this->setWageBGColor('wt_bill_ovr');
            $this->setWageBGColor('wt_bill_ovr_midnight');
            $this->setWageBGColor('wt_bill_holiday');
            $this->setWageBGColor('wt_bill_holiday_midnight');
        }
    }

    /**
     * reset content of the form
     */
    public function resetEmployeePay()
    {
        $this->client_id = '';
        $this->clientplace_id = '';
        $this->clientworktype_id = '';
        $this->updateClientWorkType();
        $this->resetFields();
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployeePay() {
        return redirect()->route('hourlywage', ['id' => $this->employee_id]);
    }
}
