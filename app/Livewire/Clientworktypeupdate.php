<?php

namespace App\Livewire;

use Livewire\Component;

use App\Traits\rukuruUtilities;

use App\Models\applogs;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;

class Clientworktypeupdate extends ClientworktypeBase
{
    use rukuruUtilities;

    /**
     * Load client worktype data
     */
    public function loadClientWorktype($id)
    {
        $clientWorktype = modelClientWorktypes::find($id);
        if(!$clientWorktype)
        {
            session()->flash('error', __('Client work type') . ' ' . __('Not Found'));
            return redirect()->route('clientworktype');
        }
        $this->clientWorktypeId = $id;
        $this->client_id = $clientWorktype->client_id;
        $this->clientplace_id = $clientWorktype->clientplace_id;
        $this->wt_cd = $clientWorktype->wt_cd;
        $this->wt_name = $clientWorktype->wt_name;
        $this->wt_kana = $clientWorktype->wt_kana;
        $this->wt_alpha = $clientWorktype->wt_alpha;
        $this->wt_day_night = $clientWorktype->wt_day_night;
        $this->wt_work_start = $clientWorktype->wt_work_start;
        $this->wt_work_end = $clientWorktype->wt_work_end;

        $this->wt_lunch_break_start = $clientWorktype->wt_lunch_break_start;
        $this->wt_lunch_break_end = $clientWorktype->wt_lunch_break_end;
        $this->wt_lunch_break = $clientWorktype->wt_lunch_break;
        $this->wt_evening_break_start = $clientWorktype->wt_evening_break_start;
        $this->wt_evening_break_end = $clientWorktype->wt_evening_break_end;
        $this->wt_evening_break = $clientWorktype->wt_evening_break;
        $this->wt_night_break_start = $clientWorktype->wt_night_break_start;
        $this->wt_night_break_end = $clientWorktype->wt_night_break_end;
        $this->wt_night_break = $clientWorktype->wt_night_break;
        $this->wt_midnight_break_start = $clientWorktype->wt_midnight_break_start;
        $this->wt_midnight_break_end = $clientWorktype->wt_midnight_break_end;
        $this->wt_midnight_break = $clientWorktype->wt_midnight_break;

        $this->wt_pay_std = $clientWorktype->wt_pay_std;
        $this->wt_pay_ovr = $clientWorktype->wt_pay_ovr;
        $this->wt_pay_ovr_midnight = $clientWorktype->wt_pay_ovr_midnight;
        $this->wt_pay_holiday = $clientWorktype->wt_pay_holiday;
        $this->wt_pay_holiday_midnight = $clientWorktype->wt_pay_holiday_midnight;

        $this->wt_bill_std = $clientWorktype->wt_bill_std;
        $this->wt_bill_ovr = $clientWorktype->wt_bill_ovr;
        $this->wt_bill_ovr_midnight = $clientWorktype->wt_bill_ovr_midnight;
        $this->wt_bill_holiday = $clientWorktype->wt_bill_holiday;
        $this->wt_bill_holiday_midnight = $clientWorktype->wt_bill_holiday_midnight;
        
        $this->wt_notes = $clientWorktype->wt_notes;
    }

    /**
     * mount function
     */
    public function mount($id = null)
    {
        parent::mount($id);
        $this->loadClientWorktype($id);
        if($this->client_id)
        {            
            $this->refClientPlaces = modelClientPlaces::where('client_id', $this->client_id)->get(); // 新しいclient_idに基づいて場所のデータを取得
        }
    }
    
    public function render()
    {
        return view('livewire.clientworktypeupdate');
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClientWorktype2() {
        $this->validate();
        try {
            modelClientWorktypes::where('id', $this->clientWorktypeId)->update([
                'client_id' => $this->client_id,
                'clientplace_id' => $this->clientplace_id,
                'wt_cd' => $this->wt_cd,
                'wt_name' => $this->wt_name,
                'wt_kana' => $this->wt_kana,
                'wt_alpha' => $this->wt_alpha,
                'wt_day_night' => $this->wt_day_night,
                'wt_work_start' => $this->rukuruUtilEmptyToNull($this->wt_work_start),
                'wt_work_end' => $this->rukuruUtilEmptyToNull($this->wt_work_end),

                'wt_lunch_break_start' => $this->rukuruUtilEmptyToNull($this->wt_lunch_break_start),
                'wt_lunch_break_end' => $this->rukuruUtilEmptyToNull($this->wt_lunch_break_end),
                'wt_lunch_break' => $this->rukuruUtilEmptyToNull($this->wt_lunch_break),
                'wt_evening_break_start' => $this->rukuruUtilEmptyToNull($this->wt_evening_break_start),
                'wt_evening_break_end' => $this->rukuruUtilEmptyToNull($this->wt_evening_break_end),
                'wt_evening_break' => $this->rukuruUtilEmptyToNull($this->wt_evening_break),
                'wt_night_break_start' => $this->rukuruUtilEmptyToNull($this->wt_night_break_start),
                'wt_night_break_end' => $this->rukuruUtilEmptyToNull($this->wt_night_break_end),
                'wt_night_break' => $this->rukuruUtilEmptyToNull($this->wt_night_break),
                'wt_midnight_break_start' => $this->rukuruUtilEmptyToNull($this->wt_midnight_break_start),
                'wt_midnight_break_end' => $this->rukuruUtilEmptyToNull($this->wt_midnight_break_end),
                'wt_midnight_break' => $this->rukuruUtilEmptyToNull($this->wt_midnight_break),
                
                'wt_pay_std' => $this->rukuruUtilMoneyValue($this->wt_pay_std),
                'wt_pay_ovr' => $this->rukuruUtilMoneyValue($this->wt_pay_ovr),
                'wt_pay_ovr_midnight' => $this->rukuruUtilMoneyValue($this->wt_pay_ovr_midnight),
                'wt_pay_holiday' => $this->rukuruUtilMoneyValue($this->wt_pay_holiday),
                'wt_pay_holiday_midnight' => $this->rukuruUtilMoneyValue($this->wt_pay_holiday_midnight),
                'wt_bill_std' => $this->rukuruUtilMoneyValue($this->wt_bill_std),
                'wt_bill_ovr' => $this->rukuruUtilMoneyValue($this->wt_bill_ovr),
                'wt_bill_ovr_midnight' => $this->rukuruUtilMoneyValue($this->wt_bill_ovr_midnight),
                'wt_bill_holiday' => $this->rukuruUtilMoneyValue($this->wt_bill_holiday),
                'wt_bill_holiday_midnight' => $this->rukuruUtilMoneyValue($this->wt_bill_holiday_midnight),
                'wt_notes' => $this->wt_notes,
            ]);
            $logMessage = '作業区分 更新'
            . ($this->selectedClient ? ' ' . $this->selectedClient->cl_name : '') 
            . ($this->selectedClientPlace ? ' ' . $this->selectedClientPlace->cl_pl_name : '') 
            . ' ' . $this->wt_cd . ' ' . $this->wt_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTWORKTYPE, $logMessage);
            session()->flash('success', __('Client Work Type updated successfully.'));
            return redirect()->route('clientworktype');
        } catch (\Exception $e) {
            $logMessage = '作業区分 更新 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
