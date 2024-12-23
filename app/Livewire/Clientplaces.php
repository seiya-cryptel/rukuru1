<?php

namespace App\Livewire;

use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplaces extends Component
{
    /**
     * record set of table client and client places
     * */
    public $refClients;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteClientPlaceListener' => 'deleteClientPlace',
    ];

    /**
     * Mount the component
     * @return void
     */
    public function mount()
    {
        $this->Clients = modelClients::orderBy('cl_cd', 'asc')
            ->get();
    }

    /**
     * Render the livewire component
     * @return void
     */
    public function render()
    {
        $ClientPlaces = modelClientPlaces::with('client')
            ->select('*')
            ->join('clients as client', 'client.id', '=', 'clientplaces.client_id')
            ->orderBy('client.cl_cd', 'asc')
            ->orderBy('cl_pl_cd', 'asc')
            ->paginate(AppConsts::PAGINATION);
        return view('livewire.clientplaces', compact('ClientPlaces'));
    }

    /**
     * Open Add Client form
     * @return void
     */
    public function newClientPlace()
    {
        return redirect()->route('clientplacecreate');
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClientPlace($id) {
        return redirect()->route('clientplaceupdate', ['id' => $id]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClientPlace($id) {
        try {
            modelClientPlaces::where('id', $id)->delete();
            session()->flash('success', __('Delete') . ' ' . __('Done'));
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
