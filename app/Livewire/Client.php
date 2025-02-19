<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;
use App\Models\applogs;
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
        return redirect()->route('clientcreate', ['locale' => app()->getLocale()]);
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editClient($id) {
        return redirect()->route('clientupdate', ['id' => $id, 'locale' => app()->getLocale()]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteClient($id) {
        try {
            modelClients::destroy($id);
            $logMessage = '顧客マスタ 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_CLIENT, $logMessage);
            session()->flash('success', __('Client deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '顧客マスタ 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
