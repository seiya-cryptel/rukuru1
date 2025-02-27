<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\clients as modelClients;

class Clientcreate extends ClientBase
{
    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->cl_cd = '';
        $this->cl_full_name = '';
        $this->cl_name = '';
        $this->cl_kana = '';
        $this->cl_alpha = '';
        $this->cl_zip = '';
        $this->cl_addr1 = '';
        $this->cl_addr2 = '';
        $this->cl_psn_div = '';
        $this->cl_psn_title = '';
        $this->cl_psn_name = '';
        $this->cl_psn_kana = '';
        $this->cl_psn_mail = '';
        $this->cl_psn_tel = '';
        $this->cl_psn_fax = '';
        $this->cl_dow_statutory = '0';
        $this->cl_dow_non_statutory = '0';
        $this->cl_over_40hpw = '0';
        $this->cl_dow_first = '0';
        $this->cl_round_start = '0';
        $this->cl_round_end = '0';
        $this->cl_close_day = '0';
        $this->cl_kintai_style = '0';
        $this->cl_notes = '';
    }

    /**
     * render the view
     */
    public function render()
    {
        $this->resetFields();
        return view('livewire.clientcreate', [
            'dayOfWeek' => $this->dayOfWeek,
        ]);
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeClient()
    {
        $this->validate();
        try {
            modelClients::create([
                'cl_cd' => $this->cl_cd,
                'cl_full_name' => $this->cl_full_name,
                'cl_name' => $this->cl_name,
                'cl_kana' => $this->cl_kana,
                'cl_alpha' => $this->cl_alpha,
                'cl_zip' => $this->cl_zip,
                'cl_addr1' => $this->cl_addr1,
                'cl_addr2' => $this->cl_addr2,
                'cl_psn_div' => $this->cl_psn_div,
                'cl_psn_title' => $this->cl_psn_title,
                'cl_psn_name' => $this->cl_psn_name,
                'cl_psn_kana' => $this->cl_psn_kana,
                'cl_psn_mail' => $this->cl_psn_mail,
                'cl_psn_tel' => $this->cl_psn_tel,
                'cl_psn_fax' => $this->cl_psn_fax,
                'cl_dow_statutory' => $this->cl_dow_statutory,
                'cl_dow_non_statutory' => $this->cl_dow_non_statutory,
                'cl_over_40hpw' => $this->cl_over_40hpw,
                'cl_dow_first' => $this->cl_dow_first,
                'cl_round_start' => $this->cl_round_start,
                'cl_round_end' => $this->cl_round_end,
                'cl_close_day' => $this->cl_close_day,
                'cl_kintai_style' => $this->cl_kintai_style,
                'cl_notes' => $this->cl_notes,
            ]);
            $logMessage = '顧客マスタ 作成: ' . $this->cl_cd . ' ' . $this->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENT, $logMessage);
            session()->flash('success', __('Client created successfully.'));
            return redirect()->route('client');
        } catch (\Exception $e) {
            $logMessage = '顧客マスタ 作成 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
