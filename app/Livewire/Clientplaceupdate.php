<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplaceupdate extends ClientplaceBase 
{
    /**
     * load client place data
     */
    public function loadClientPlace($id)
    {
        $clientPlace = modelClientPlaces::find($id);
        if (!$clientPlace) {
            session()->flash('error', __('Client work place') . ' ' . __('Not Found'));
            return redirect()->route('clientplace');
        }
        $this->clientPlaceId = $id;
        $this->client_id = $clientPlace->client_id;
        $this->cl_pl_cd = $clientPlace->cl_pl_cd;
        $this->cl_pl_name = $clientPlace->cl_pl_name;
        $this->cl_pl_kana = $clientPlace->cl_pl_kana;
        $this->cl_pl_alpha = $clientPlace->cl_pl_alpha;
        $this->cl_pl_notes = $clientPlace->cl_pl_notes;
    }

    /**
     * mount the component
     */
    public function mount($id = null)
    {
        parent::mount($id);
        $this->loadClientPlace($id);
    }

    /**
     * render the view
     */
    public function render()
    {
        return view('livewire.clientplaceupdate');
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClientPlace2() {
        $this->validate();
        try {
            modelClientPlaces::where('id', $this->clientPlaceId)->update([
                'client_id' => $this->client_id,
                'cl_pl_cd' => $this->cl_pl_cd,
                'cl_pl_name' => $this->cl_pl_name,
                'cl_pl_kana' => $this->cl_pl_kana,
                'cl_pl_alpha' => $this->cl_pl_alpha,
                'cl_pl_notes' => $this->cl_pl_notes,
            ]);
            $logMessage = '顧客部門マスタ 更新: ' . $this->cl_pl_cd . ' 顧客ID ' . $this->client_id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTPLACE, $logMessage);
            session()->flash('success', __('Client Place updated successfully.'));
            return redirect()->route('clientplace');
        } catch (\Exception $e) {
            $logMessage = '顧客部門マスタ 更新 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
