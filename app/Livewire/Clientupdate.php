<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\clients as modelClients;

class Clientupdate extends Component
{
    /**
     * record set of master allow deducts
     * */
    public $Clients;
    /**
     * master allow deducts fields
     */
    public $cl_cd, 
        $cl_name, $cl_kana, $cl_alpha, 
        $cl_zip, $cl_addr1, $cl_addr2, 
        $cl_psn_div, $cl_psn_title, $cl_psn_name, $cl_psn_kana, $cl_psn_mail, $cl_psn_tel, $cl_psn_fax, 
        $cl_dow_statutory, $cl_dow_non_statutory, $cl_over_40hpw, $cl_dow_first, $cl_round_start, $cl_round_end,
        $cl_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $clientId;

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'cl_cd' => 'required',
        'cl_name' => 'required',
        'cl_kana' => 'required',
        'cl_alpha' => 'required',
    ];

    /**
     * mount the component
     */
    public function mount($id)
    {
        $client = modelClients::find($id);
        $this->clientId = $id;
        $this->cl_cd = $client->cl_cd;
        $this->cl_name = $client->cl_name;
        $this->cl_kana = $client->cl_kana;
        $this->cl_alpha = $client->cl_alpha;
        $this->cl_zip = $client->cl_zip;
        $this->cl_addr1 = $client->cl_addr1;
        $this->cl_addr2 = $client->cl_addr2;
        $this->cl_psn_div = $client->cl_psn_div;
        $this->cl_psn_title = $client->cl_psn_title;
        $this->cl_psn_name = $client->cl_psn_name;
        $this->cl_psn_kana = $client->cl_psn_kana;
        $this->cl_psn_mail = $client->cl_psn_mail;
        $this->cl_psn_tel = $client->cl_psn_tel;
        $this->cl_psn_fax = $client->cl_psn_fax;
        $this->cl_dow_statutory = $client->cl_dow_statutory;
        $this->cl_dow_non_statutory = $client->cl_dow_non_statutory;
        $this->cl_over_40hpw = $client->cl_over_40hpw;
        $this->cl_dow_first = $client->cl_dow_first;
        $this->cl_round_start = $client->cl_round_start;
        $this->cl_round_end = $client->cl_round_end;
        $this->cl_notes = $client->cl_notes;
    }

    /**
     * render the view
     */
    public function render()
    {
        return view('livewire.clientupdate');
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClient2() {
        $this->validate();
        try {
            modelClients::where('id', $this->clientId)->update([
                'cl_cd' => $this->cl_cd,
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
                'cl_notes' => $this->cl_notes,
            ]);
            $logMessage = '顧客マスタ 更新: ' . $this->cl_cd . ' ' . $this->cl_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENT, $logMessage);
            session()->flash('success', __('Update') . ' ' . __('Done'));
            return redirect()->route('client');
        } catch (\Exception $e) {
            $logMessage = '顧客マスタ 更新 エラー: ' . $e->getMessage();
            logger(logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
        $this->updateClient = false;
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClient() {
        return redirect()->route('client');
    }
}
