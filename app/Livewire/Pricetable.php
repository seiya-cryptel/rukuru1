<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\pricetables as modelPriceTables;

class Pricetable extends Component
{
    /**
     * record set of table clients and client places and pricetable
     * */
    public $Clients;
    public $ClientPlaces = [];
    public $PriceTables;

    /**
     * master fields
     */
    public $client_id, $clientplace_id,
        $wt_cd, 
        $bill_name, $bill_print_name, $bill_unitprice, $display_order, 
        $notes;
        
    /**
     * master pricetable id
     */
    public $priceTableId;
    
    /**
     * add or update flag
     */
    public $updatePriceTable = false, $addPriceTable = false;    

    /**
     * delete action listener
     */
    protected $listeners = [
        'deletePriceTableListener' => 'deletePriceTable',
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'client_id' => 'required',
        'clientplace_id' => 'required',
        'wt_cd' => 'required',
        'bill_name' => 'required',
        'bill_print_name' => 'required',
        'bill_unitprice' => 'required',
        'display_order' => 'required',
    ];

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->client_id = '';
        $this->clientplace_id = '';
        $this->wt_cd = '';
        $this->bill_name = '';
        $this->bill_print_name = '';
        $this->bill_unitprice = '';
        $this->display_order = '';
        $this->notes = '';
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
        $this->PriceTables = modelPriceTables::with('client')->with('clientplace')
        ->select('id', 'client_id', 'clientplace_id', 'wt_cd', 
            'bill_name', 'bill_print_name', 'bill_unitprice', 'display_order', 'notes') 
            ->get();
        return view('livewire.pricetable');
    }

    /**
     * Open Add PriceTable form
     * @return void
     */
    public function newPriceTable()
    {
        $this->resetFields();
        $this->addPriceTable = true;
        $this->updatePriceTable = false;
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storePriceTable()
    {
        $this->validate();
        try {
            modelPriceTables::create([
                'client_id' => $this->client_id,
                'clientplace_id' => $this->clientplace_id,
                'wt_cd' => $this->wt_cd,
                'bill_name' => $this->bill_name,
                'bill_print_name' => $this->bill_print_name,
                'bill_unitprice' => $this->bill_unitprice,
                'display_order' => $this->display_order,
                'notes' => $this->notes,
            ]);
            $this->resetFields();
            $this->addPriceTable = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editPriceTable($id) {
        try {
            $PriceTable = modelPriceTables::findOrFail($id);
            if(!$PriceTable) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->PriceTableId = $id;
                $this->client_id = $PriceTable->client_id;
                $this->clientplace_id = $PriceTable->clientplace_id;
                $this->wt_cd = $PriceTable->wt_cd;
                $this->bill_name = $PriceTable->bill_name;
                $this->bill_print_name = $PriceTable->bill_print_name;
                $this->bill_unitprice = $PriceTable->bill_unitprice;
                $this->display_order = $PriceTable->display_order;
                
                $this->updatePriceTable = true;
                $this->addPriceTable = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updatePriceTable() {
        $this->validate();
        try {
            modelPriceTables::where('id', $this->PriceTableId)->update([
                'client_id' => $this->client_id,
                'clientplace_id' => $this->clientplace_id,
                'wt_cd' => $this->wt_cd,
                'bill_name' => $this->bill_name,
                'bill_print_name' => $this->bill_print_name,
                'bill_unitprice' => $this->bill_unitprice,
                'display_order' => $this->display_order,
                'notes' => $this->notes,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelPriceTable() {
        $this->resetFields();
        $this->addPriceTable = false;
        $this->updatePriceTable = false;
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deletePriceTable($id) {
        try {
            modelPriceTables::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
