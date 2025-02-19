<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\applogs;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

abstract class ClientplaceBase extends Component
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
    ];

    /**
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'client_id.required' => __('Required'),
            'cl_pl_cd.required' => __('Required'),
            'cl_pl_name.required' => __('Required'),
        ];
    }

    /**
     * mount the component
     */
    public function mount($id = null)
    {
        $this->refClients = modelClients::orderBy('cl_cd', 'asc')->get();
        $this->clientPlaceId = $id;
    }

    /**
     * render the view
     */
    abstract public function render();

    /**
     * cancel add/edit form
     */
    public function cancelClientPlace()
    {
        return redirect()->route('clientplace');
    }
}
