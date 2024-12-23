<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\clients as modelClients;

class Client extends Component
{
    use WithPagination;
    
    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteClientListener' => 'deleteClient',
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'cl_cd' => 'required',
        'cl_name' => 'required',
        'cl_kana' => 'required',
        'cl_alpha' => 'required',
    ];

    public function render()
    {
        $Clients = modelClients::orderBy('cl_cd', 'asc')
                ->paginate(AppConsts::PAGINATION);
        return view('livewire.client', compact('Clients'));
    }

    /**
     * Open Add Client form
     * @return void
     */
    public function newClient()
    {
        return redirect()->route('clientcreate');
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClient($id) {
        return redirect()->route('clientupdate', ['id' => $id]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClient($id) {
        try {
            modelClients::where('id', $id)->delete();
            session()->flash('success', __('Delete') . ' ' . __('Done'));
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
