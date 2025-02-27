<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\applogs;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorkTypes;

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
        $this->client_id = session(AppConsts::SESS_CLIENT_ID, '');
        $this->clientplace_id = session(AppConsts::SESS_CLIENT_PLACE_ID, '');

        $this->Clients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha')->get();
        $this->ClientPlaces = [];
        if($this->client_id) {
            $this->ClientPlaces = modelClientPlaces::where('client_id', $this->client_id)
                ->orderBy('cl_pl_cd', 'asc')
                ->get();
        }
    }

    public function render()
    {
        $Query = modelClientWorkTypes::with('client')->with('clientplace')
            ->select('*')
            ->join('clients as client', 'client.id', '=', 'clientworktypes.client_id')
            ->leftJoin('clientplaces as clientplace', 'clientplace.id', '=', 'clientworktypes.clientplace_id')
            ->select('clientworktypes.id as clientworktype_id', 'client.*', 'clientplace.*', 'clientworktypes.*');
            ;
        if ($this->client_id) {
            $Query->where('clientworktypes.client_id', $this->client_id);
        }
        if ($this->clientplace_id) {
            $Query->where('clientworktypes.clientplace_id', $this->clientplace_id);
        }
        $Query->orderBy('client.cl_cd', 'asc');
        $Query->orderBy('clientplace.cl_pl_cd', 'asc');
        $Query->orderBy('wt_cd', 'asc');
        $ClientWorktypes = $Query->paginate(10);
        return view('livewire.clientworktypes', compact('ClientWorktypes'));
    }

    /**
     * clear search keyword
     */
    public function clearSearch()
    {
        $this->client_id = '';
        $this->clientplace_id = '';

        session([
            AppConsts::SESS_CLIENT_ID => $this->client_id,
            AppConsts::SESS_CLIENT_PLACE_ID => $this->clientplace_id,
        ]);
    }

    /**
     * client_id change event
     */
    public function updateClientId($client_id)
    {
        $this->client_id = $client_id;
        $this->clientplace_id = '';
        session([
            AppConsts::SESS_CLIENT_ID => $this->client_id,
            AppConsts::SESS_CLIENT_PLACE_ID => $this->clientplace_id,
        ]);

        $this->ClientPlaces = [];
        if($this->client_id) {
            $this->ClientPlaces = modelClientPlaces::where('client_id', $this->client_id)
                ->orderBy('cl_pl_cd', 'asc')
                ->get();
        }
    }

    /**
     * clientplace_id change event
     */
    public function updateClientPlaceId($clientplace_id)
    {
        $this->clientplace_id = $clientplace_id;
        session([
            AppConsts::SESS_CLIENT_PLACE_ID => $this->clientplace_id,
        ]);
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
        $clientWorkType = modelClientWorkTypes::with(['client', 'clientplace'])
            ->select('client.*', 'clientplace.*', 'clientworktypes.*')
            ->leftJoin('clients as client', 'client.id', '=', 'clientworktypes.client_id')
            ->leftJoin('clientplaces as clientplace', 'clientplace.id', '=', 'clientworktypes.clientplace_id')
            ->find($id);
        try {
            modelClientWorkTypes::destroy($id);
            $logMessage = '作業区分 削除'
            . ' ' . $clientWorkType->cl_name 
            . ' ' . $clientWorkType->cl_pl_name 
            . ' ' . $clientWorkType->wt_cd . ' ' . $clientWorkType->wt_name;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTWORKTYPE, $logMessage);
            session()->flash('success', __('Client Work Type deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '作業区分 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
