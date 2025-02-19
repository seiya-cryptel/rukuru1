<?php

namespace App\Livewire;

use Livewire\Component;
use App\Consts\AppConsts;
use App\Models\applogs;
use App\Models\masterallowdeducts as modelMad;

class Allowdeductcreate extends AllowdeductBase
{

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->mad_cd = '';
        $this->mad_allow = false;
        $this->mad_deduct = false;
        $this->mad_name = '';
        $this->mad_notes = '';
    }

    /**
     * mount the component
     */
    public function mount()
    {
        $this->resetFields();
    }

    public function render()
    {
        return view('livewire.allowdeductcreate');
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeMad()
    {
        $this->validate();
        try {
            modelMad::create([
                'mad_cd' => $this->mad_cd,
                'mad_allow' => $this->mad_allow,
                'mad_deduct' => $this->mad_deduct,
                'mad_name' => $this->mad_name,
                'mad_notes' => $this->mad_notes
            ]);
            $logMessage = '手当控除 作成: ' . $this->mad_cd . ' ' . $this->mad_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_ALLOWDEDUCT, $logMessage);
            session()->flash('success', __('Allow Deduct created successfully.'));
            return redirect()->route('masterallowdeduct');
        } catch (\Exception $e) {
            $logMessage = '手当控除 作成 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
