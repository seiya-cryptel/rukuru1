<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\applogs;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplacecreate extends ClientplaceBase
{
    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->cl_pl_cd = '';
        $this->cl_pl_name = '';
        $this->cl_pl_kana = '';
        $this->cl_pl_alpha = '';
        $this->cl_pl_notes = '';
    }

    /**
     * mount the component
     */
    public function mount($id = null)
    {
        parent::mount($id);
        $this->resetFields();
    }

    /**
     * render the view
     */
    public function render()
    {
        return view('livewire.clientplacecreate');
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeClientPlace()
    {
        $this->validate();
        try {
            modelClientPlaces::create([
                'client_id' => $this->client_id,
                'cl_pl_cd' => $this->cl_pl_cd,
                'cl_pl_name' => $this->cl_pl_name,
                'cl_pl_kana' => $this->cl_pl_kana,
                'cl_pl_alpha' => $this->cl_pl_alpha,
                'cl_pl_notes' => $this->cl_pl_notes,
            ]);
            $logMessage = '顧客部門マスタ 作成: ' . $this->cl_pl_cd . ' 顧客ID ' . $this->client_id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTPLACE, $logMessage);
            session()->flash('success', __('Client Place created successfully.'));
            return redirect()->route('clientplace');
        } catch (\Exception $e) {
            $logMessage = '顧客部門マスタ 作成 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
