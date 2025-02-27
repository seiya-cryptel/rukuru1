<?php

namespace App\Livewire;

use Livewire\Component;
use App\Consts\AppConsts;
use App\Models\applogs;
use App\Models\masterallowdeducts as modelMad;

class Allowdeductupdate extends AllowdeductBase
{

    /**
     * mount the component
     */
    public function mount($id)
    {
        $mad = modelMad::find($id);
        $this->madId = $id;
        $this->mad_cd = $mad->mad_cd;
        $this->mad_allow = $mad->mad_allow;
        $this->mad_deduct = $mad->mad_deduct;
        $this->mad_name = $mad->mad_name;
        $this->mad_notes = $mad->mad_notes;
    }

    public function render()
    {
        return view('livewire.allowdeductupdate');
    }

    /**
     * update the master data
     * @return void
     */
    public function updateMad2() {
        $this->validate();
        try {
            modelMad::where('id', $this->madId)->update([
                'mad_cd' => $this->mad_cd,
                'mad_allow' => $this->mad_allow,
                'mad_deduct' => $this->mad_deduct,
                'mad_name' => $this->mad_name,
                'mad_notes' => $this->mad_notes
            ]);
            $logMessage = '手当控除 更新: ' . $this->mad_cd . ' ' . $this->mad_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_ALLOWDEDUCT, $logMessage);
            session()->flash('success', __('Allow Deduct updated successfully.'));
            return redirect()->route('masterallowdeduct');
        } catch (\Exception $e) {
            $logMessage = '手当控除 更新 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
