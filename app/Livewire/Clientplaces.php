<?php

namespace App\Livewire;

use Livewire\Component;

use App\Consts\AppConsts;
use App\Models\applogs;
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
            ->select('clientplaces.id as clientplace_id', 'client.*', 'clientplaces.*')
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
        return redirect()->route('clientplacecreate', ['locale' => app()->getLocale()]);
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClientPlace($id) {
        return redirect()->route('clientplaceupdate', ['id' => $id, 'locale' => app()->getLocale()]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClientPlace($id) {
        try {
            modelClientPlaces::destroy($id);
            $logMessage = '顧客部門マスタ 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENTPLACE, $logMessage);
            session()->flash('success', __('Client Place deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '顧客部門マスタ 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
