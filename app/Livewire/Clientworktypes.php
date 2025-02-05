<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;

/**
 * Clientworktypes class
 * 顧客作業種類一覧
 */
class Clientworktypes extends Component
{
    use WithPagination;

    /**
     * record set of table clients and client places
     * */
    public $Clients;
    public $ClientPlaces = [];
    // public $ClientWorktypes;

    /**
     * client id and client place id for query
     */
    public $client_id, $clientplace_id;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteClientWorkTypeListener' => 'deleteClientWorkType',
    ];

    /**
     * Mount the component
     * @return void
     */
    public function mount()
    {
        $this->Clients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha')->get();
        $this->ClientPlaces = modelClientPlaces::select('id', 'cl_pl_cd', 'cl_pl_name', 'cl_pl_kana', 'cl_pl_alpha')->get();
    }

    public function render()
    {
        $Query = modelClientWorktypes::with('client')->with('clientplace')
            // ->select('id', 'client_id', 'clientplace_id', 'wt_cd', 'wt_name', 'wt_kana', 'wt_alpha', 'wt_notes')
            ;
        if ($this->client_id) {
            $Query->where('client_id', $this->client_id);
        }
        if ($this->clientplace_id) {
            $Query->where('clientplace_id', $this->clientplace_id);
        }
        $ClientWorktypes = $Query->paginate(10);
        return view('livewire.clientworktypes', compact('ClientWorktypes'));
    }

    /**
     * client_id change event
     */
    public function updateClientId($client_id)
    {
        $this->clientplace_id = '';
        $this->ClientPlaces = modelClientPlaces::where('client_id', $this->client_id)
            ->orderBy('cl_pl_cd', 'asc')
            ->get();
    }

    /**
     * clientplace_id change event
     */
    public function updateClientPlaceId($clientplace_id)
    {
    }

    /**
     * Open Add ClientWorktype form
     * @return void
     */
    public function newClientWorkType()
    {
        return redirect()->route('clientworktypecreate', ['locale' => app()->getLocale()]);
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClientWorkType($id) {
        return redirect()->route('clientworktypeupdate', ['id' => $id, 'locale' => app()->getLocale()]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClientWorkType($id) {
        try {
            $clientWorkType = modelClientWorktypes::find($id);
            modelClientWorktypes::where('id', $id)->delete();

            $strMessage = '作業区分 削除'
            . ($this->selectedClient ? ' ' . $this->selectedClient->cl_name : '') 
            . ($this->selectedClientPlace ? ' ' . $this->selectedClientPlace->cl_pl_name : '') 
            . ' ' . $clientWorkType->wt_cd . ' ' . $clientWorkType->wt_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTWORKTYPE, $logMessage);
            session()->flash('success', __('Delete'). ' ' . __('Done'));
        } catch (\Exception $e) {
            $logMessage = '作業区分 削除 エラー: ' . $e->getMessage();
            logger(logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
