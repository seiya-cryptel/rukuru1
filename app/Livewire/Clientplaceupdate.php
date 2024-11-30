<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplaceupdate extends Component
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
     * mount the component
     */
    public function mount($id)
    {
        $this->refClients = modelClients::orderBy('cl_cd', 'asc')->get();
        $clientPlace = modelClientPlaces::find($id);
        $this->clientPlaceId = $id;
        $this->client_id = $clientPlace->client_id;
        $this->cl_pl_cd = $clientPlace->cl_pl_cd;
        $this->cl_pl_name = $clientPlace->cl_pl_name;
        $this->cl_pl_kana = $clientPlace->cl_pl_kana;
        $this->cl_pl_alpha = $clientPlace->cl_pl_alpha;
        $this->cl_pl_notes = $clientPlace->cl_pl_notes;
    }

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
            return redirect()->route('clientplace');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
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
