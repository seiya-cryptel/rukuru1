<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;

class Clientworktypes extends Component
{
    /**
     * record set of table clients and client places
     * */
    public $Clients;
    public $ClientPlaces = [];
    public $ClientWorktypes;
    /**
     * master fields
     */
    public $client_id, $clientplace_id,
        $wt_cd, 
        $wt_name, $wt_kana, $wt_alpha, 
        $wt_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $clientWorktypeId, $updateClientWorktype = false, $addClientWorktype = false;    

    /**
     * delete action listener
     */
    protected $listeners = [
        'deletClientWorktypeListener' => 'deleteClientWorktype',
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'wt_cd' => 'required',
        'wt_name' => 'required',
        'wt_kana' => 'required',
        'wt_alpha' => 'required',
    ];

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->wt_cd = '';
        $this->wt_name = '';
        $this->wt_kana = '';
        $this->wt_alpha = '';
        $this->wt_notes = '';
    }

    /**
     * Mount the component
     * @return void
     */
    public function mount()
    {
        $this->Clients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha')->get();
        $this->ClientPlaces = modelClientPlaces::select('id', 'cl_pl_cd', 'cl_pl_name', 'cl_pl_kana', 'cl_pl_alpha')->get();
    }

    public function updateClientId($value)
    {
        // client_idが更新されたときに呼び出される
        $this->ClientPlaces = modelClientPlaces::where('client_id', $value)->get(); // 新しいclient_idに基づいて場所のデータを取得
        $this->clientplace_id = null; // clientplace_idをリセット
    }

    public function render()
    {
        $this->ClientWorktypes = modelClientWorktypes::with('client')->with('clientplace')
        ->select('id', 'client_id', 'clientplace_id', 'wt_cd', 'wt_name', 'wt_kana', 'wt_alpha', 
            'wt_notes')->get();
        return view('livewire.clientworktypes');
    }

    /**
     * Open Add ClientWorktype form
     * @return void
     */
    public function newClientWorktype()
    {
        $this->resetFields();
        $this->addClientWorktype = true;
        $this->updateClientWorktype = false;
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeClientWorktype()
    {
        $this->validate();
        try {
            modelClientWorktypes::create([
                'client_id' => $this->client_id,
                'clientplace_id' => $this->clientplace_id,
                'wt_cd' => $this->wt_cd,
                'wt_name' => $this->wt_name,
                'wt_kana' => $this->wt_kana,
                'wt_alpha' => $this->wt_alpha,
                'wt_notes' => $this->wt_notes,
            ]);
            $this->resetFields();
            $this->addClientWorktype = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClientWorktype($id) {
        try {
            $ClientWorktype = modelClientWorktypes::findOrFail($id);
            if(!$ClientWorktype) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->clientWorktypeId = $id;
                $this->client_id = $ClientWorktype->client_id;
                $this->clientplace_id = $ClientWorktype->clientplace_id;
                $this->wt_cd = $ClientWorktype->wt_cd;
                $this->wt_name = $ClientWorktype->wt_name;
                $this->wt_kana = $ClientWorktype->wt_kana;
                $this->wt_alpha = $ClientWorktype->wt_alpha;
                
                $this->updateClientWorktype = true;
                $this->addClientWorktype = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClientWorktype() {
        $this->validate();
        try {
            modelClientWorktypes::where('id', $this->ClientWorktypeId)->update([
                'client_id' => $this->client_id,
                'clientplace_id' => $this->clientplace_id,
                'wt_cd' => $this->wt_cd,
                'wt_name' => $this->wt_name,
                'wt_kana' => $this->wt_kana,
                'wt_alpha' => $this->wt_alpha,
                'wt_notes' => $this->wt_notes,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClientWorktype() {
        $this->resetFields();
        $this->addClientWorktype = false;
        $this->updateClientWorktype = false;
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClientWorktype($id) {
        try {
            modelClientWorktypes::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
