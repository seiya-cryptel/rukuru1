<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clients as modelClients;

class Clientplaces extends Component
{
    /**
     * record set of table client and client places
     * */
    public $Clients;
    public $ClientPlaces;
    /**
     * master fields
     */
    public $client_id, $cl_pl_cd, 
        $cl_pl_name, $cl_pl_kana, $cl_pl_alpha, 
        $cl_pl_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $clientPlaceId, $updateClientPlace = false, $addClientPlace = false;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deletClientPlaceListener' => 'deleteClientPlace',
    ];

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

    /**
     * Mount the component
     * @return void
     */
    public function mount()
    {
        $this->Clients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha')->get();
    }

    /**
     * Render the livewire component
     * @return void
     */
    public function render()
    {
        $this->ClientPlaces = modelClientPlaces::with('client')
        ->select('id', 'client_id', 'cl_pl_cd', 'cl_pl_name', 'cl_pl_kana', 'cl_pl_alpha', 
            'cl_pl_notes')->get();
        return view('livewire.clientplaces');
    }

    /**
     * Open Add ClientPlace form
     * @return void
     */
    public function newClientPlace()
    {
        $this->resetFields();
        $this->addClientPlace = true;
        $this->updateClientPlace = false;
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
            $this->resetFields();
            $this->addClientPlace = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClientPlace($id) {
        try {
            $ClientPlace = modelClientPlaces::findOrFail($id);
            if(!$ClientPlace) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->clientPlaceId = $id;
                $this->client_id = $ClientPlace->client_id;
                $this->cl_pl_cd = $ClientPlace->cl_pl_cd;
                $this->cl_pl_name = $ClientPlace->cl_pl_name;
                $this->cl_pl_kana = $ClientPlace->cl_pl_kana;
                $this->cl_pl_alpha = $ClientPlace->cl_pl_alpha;
                
                $this->updateClientPlace = true;
                $this->addClientPlace = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClientPlace() {
        $this->validate();
        try {
            modelClientPlaces::where('id', $this->ClientPlaceId)->update([
                'client_id' => $this->client_id,
                'cl_pl_cd' => $this->cl_pl_cd,
                'cl_pl_name' => $this->cl_pl_name,
                'cl_pl_kana' => $this->cl_pl_kana,
                'cl_pl_alpha' => $this->cl_pl_alpha,
                'cl_pl_notes' => $this->cl_pl_notes,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClientPlace() {
        $this->resetFields();
        $this->addClientPlace = false;
        $this->updateClientPlace = false;
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClientPlace($id) {
        try {
            modelClientPlaces::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
