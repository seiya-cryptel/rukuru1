<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplacecreate extends Component
{
    /**
     * reference to client records
     */
    public $refClients;
    /**
     * master fields
     */
    public $client_id, $cl_pl_cd, 
        $cl_pl_name, $cl_pl_kana, $cl_pl_alpha, 
        $cl_pl_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $clientPlaceId;

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'client_id' => 'required',
        'cl_pl_cd' => 'required',
        'cl_pl_name' => 'required',
        'cl_pl_kana' => 'required',
        'cl_pl_alpha' => 'required',
    ];

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

    public function render()
    {
        $this->refClients = modelClients::orderBy('cl_cd', 'asc')->get();
        $this->resetFields();
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
            session()->flash('success', __('Create') . ' ' . __('Done'));
            return redirect()->route('clientplace');
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * cancel add/edit form
     */
    public function cancelClientPlace()
    {
        return redirect()->route('clientplace');
    }
}
