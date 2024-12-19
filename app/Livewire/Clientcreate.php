<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clients as modelClients;

class Clientcreate extends Component
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
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->cl_cd = '';
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
        $this->cl_dow_non_statutory = '6';
        $this->cl_over_40hpw = '1';
        $this->cl_dow_first = '0';
        $this->cl_round_start = '0';
        $this->cl_round_end = '0';
        $this->cl_notes = '';
    }

    /**
     * render the view
     */
    public function render()
    {
        $this->resetFields();
        return view('livewire.clientcreate');
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
            session()->flash('success', __('Create') . ' ' . __('Done'));
            return redirect()->route('client');
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClient() {
        return redirect()->route('client');
    }
}
