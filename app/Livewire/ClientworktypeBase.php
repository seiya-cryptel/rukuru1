<?php

namespace App\Livewire;

use App\Traits\rukuruUtilites;

use Livewire\Component;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;

abstract class ClientworktypeBase extends component
{
    use rukuruUtilites;
    /**
     * record set of table clients and client places
     * */
    public $refClients;
    public $refClientPlaces = [];

    /**
     * master fields
     */
    public $client_id, $clientplace_id,
        $wt_cd, 
        $wt_name, $wt_kana, $wt_alpha, 
        $wt_day_night, $wt_work_start, $wt_work_end,
        $wt_lunch_break_start, $wt_lunch_break_end, $wt_lunch_break, 
        $wt_evening_break_start, $wt_evening_break_end, $wt_evening_break, 
        $wt_night_break_start, $wt_night_break_end, $wt_night_break, 
        $wt_midnight_break_start, $wt_midnight_break_end, $wt_midnight_break, 
        $wt_pay_std, $wt_pay_ovr, $wt_pay_ovr_midnight, $wt_pay_holiday, $wt_pay_holiday_midnight,
        $wt_bill_std, $wt_bill_ovr, $wt_bill_ovr_midnight, $wt_bill_holiday, $wt_bill_holiday_midnight,
        $wt_notes;

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'wt_cd' => 'required',
        'wt_name' => 'required',
        'wt_kana' => 'required',
        'wt_alpha' => 'required',
    ];

    /**
     * 顧客IDが変更されたときに呼び出される
     * @param int $client_id
     * @return void
     * 事業所リストを更新する
     */
    public function updateClientId($client_id)
    {
        // client_idが更新されたときに呼び出される
        $this->refClientPlaces = modelClientPlaces::where('client_id', $client_id)->get(); // 新しいclient_idに基づいて場所のデータを取得
        $this->clientplace_id = null; // clientplace_idをリセット
    }

    /**
     * 時刻項目が変更されたときに呼び出される
     * @param string $time, string $field
     * @return void
     */
    public function timeChange($time, $field)
    {
        try {
            $this->$field = $this->rukuruUtilTimeNormalize($time);
            $this->resetErrorBag($field); // エラーをリセット
        } catch (\Exception $e) {
            $this->addError($field, $e->getMessage());
        }
    }

    /**
     * 金額項目が変更されたときに呼び出される
     * @param string $money, string $field
     * @return void
     */
    public function moneyChange($money, $field)
    {
        $money = preg_replace('/[^0-9.]/', '', $money);
        $this->$field = empty($money) ? '' : $money;
    }

    /**
     * mount function
     */
    public function mount($id = null)
    {
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
    }

    /**
     * render function
     */
    abstract public function render();

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClientWorktype() {
        return redirect()->route('clientworktype');
    }
}