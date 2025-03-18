<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\employees as modelEmployees;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes;

use App\Traits\rukuruUtilities;

abstract class EmployeeBase extends Component
{
    use rukuruUtilities;

    /**
     * プリセット作業種別の最大数
     */
    public const MAX_SLOTS = 7;

    /**
     * record set of table clients and client places
     * */
    public $refClients;
    public $refClientPlaces;
    public $refWtCdList;

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
        $empl_paid_leave_pay, $empl_main_client_id, $empl_main_clientplace_id,
        $empl_main_client_name,
        $empl_wt_cd_list,
        $empl_notes;
    public $wt_cd_list = [];

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
    ];

    /**
     * List of add/edit form validation messages
     */
    protected $messages = [
        'empl_cd.required' => '必須です',
        'empl_name_last.required' => '必須です',
    ];

    /**
     * 主な顧客部門リストを更新する
     */
    public function updateMainClientPlaceList()
    {
        if($this->empl_main_client_id) {
            $this->refClientPlaces = modelClientPlaces::where('client_id', $this->empl_main_client_id)->orderBy('cl_pl_cd', 'asc')->get();
        } else {
            $this->refClientPlaces = [];
        }
    }

    /**
     * mount function
     */
    public function mount($id = null)
    {
        $this->refClients = modelClients::orderBy('cl_cd', 'asc')->get();
        $this->refWtCdList = clientworktypes::where('client_id', 3)
            ->orderBy('wt_cd', 'asc')
            ->get();
    }

    abstract public function render();

    /**
     * 金額項目が変更されたときに呼び出される
     * @param string $money, string $field
     * @return void
     */
    public function moneyChange($money, $field)
    {
        $money = $this->rukuruUtilMoneyValue($money);
        $this->$field = empty($money) ? '' : number_format($money);
    }

    /**
     * 主な顧客が変更されたときの処理
     */
    public function emplMainClientIdChange($value)
    {
        $this->updateMainClientPlaceList();
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployee() {
        return redirect()->route('employee');
    }
}
