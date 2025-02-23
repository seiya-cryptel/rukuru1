<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\clients as modelClients;

abstract class ClientBase extends Component
{
    /**
     * record set of master allow deducts
     * */
    public $Clients;

    /**
     * day of week array
     */
    public $dayOfWeek = [
        '0' => '（なし）',
        '1' => '日',
        '2' => '月',
        '3' => '火',
        '4' => '水',
        '5' => '木',
        '6' => '金',
        '7' => '土',
    ];

    /**
     * master allow deducts fields
     */
    public $cl_cd, 
        $cl_full_name,
        $cl_name, $cl_kana, $cl_alpha, 
        $cl_zip, $cl_addr1, $cl_addr2, 
        $cl_psn_div, $cl_psn_title, $cl_psn_name, $cl_psn_kana, $cl_psn_mail, $cl_psn_tel, $cl_psn_fax, 
        $cl_dow_statutory, $cl_dow_non_statutory, $cl_over_40hpw, $cl_dow_first, $cl_round_start, $cl_round_end,
        $cl_close_day, $cl_kintai_style,
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
    ];

    /**
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'cl_cd.required' => __('Required'),
            'cl_name.required' => __('Required'),
        ];
    }

    /**
     * render the view
     */
    abstract public function render();

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClient() {
        return redirect()->route('client');
    }
}
