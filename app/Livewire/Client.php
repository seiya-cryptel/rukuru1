<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clients as modelClients;

class Client extends Component
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
        $cl_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $clientId, $updateClient = false, $addClient = false;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deletClientListener' => 'deleteClient',
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
        $this->cl_notes = '';
    }

    public function render()
    {
        $this->Clients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha', 
            'cl_zip', 'cl_addr1', 'cl_addr2', 
            'cl_psn_div', 'cl_psn_title', 'cl_psn_name', 'cl_psn_kana', 'cl_psn_mail', 'cl_psn_tel', 'cl_psn_fax', 
            'cl_notes')->get();
        return view('livewire.client');
    }

    /**
     * Open Add Client form
     * @return void
     */
    public function newClient()
    {
        $this->resetFields();
        $this->addClient = true;
        $this->updateClient = false;
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
                'cl_notes' => $this->cl_notes,
            ]);
            $this->resetFields();
            $this->addClient = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClient($id) {
        try {
            $Client = modelClients::findOrFail($id);
            if(!$Client) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->clientId = $id;
                $this->cl_cd = $Client->cl_cd;
                $this->cl_name = $Client->cl_name;
                $this->cl_kana = $Client->cl_kana;
                $this->cl_alpha = $Client->cl_alpha;
                $this->cl_zip = $Client->cl_zip;
                $this->cl_addr1 = $Client->cl_addr1;
                $this->cl_addr2 = $Client->cl_addr2;
                $this->cl_psn_div = $Client->cl_psn_div;
                $this->cl_psn_title = $Client->cl_psn_title;
                $this->cl_psn_name = $Client->cl_psn_name;
                $this->cl_psn_kana = $Client->cl_psn_kana;
                $this->cl_psn_mail = $Client->cl_psn_mail;
                $this->cl_psn_tel = $Client->cl_psn_tel;
                $this->cl_psn_fax = $Client->cl_psn_fax;
                $this->cl_notes = $Client->cl_notes;
                
                $this->updateClient = true;
                $this->addClient = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updateClient() {
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
                'cl_notes' => $this->cl_notes,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelClient() {
        $this->resetFields();
        $this->addClient = false;
        $this->updateClient = false;
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClient($id) {
        try {
            modelClients::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
