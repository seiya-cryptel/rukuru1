<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\bills as modelBills;

class Bills extends Component
{
    use WithPagination;

    /**
     * work year, month and client information
     * */
    public $workYear, $workMonth, $client_id, $clientplace_id;

    /**
     * client records for select box
     * */
    public $refClients;

    /**
     * client place records for select box
     * */
    public $refClientPlaces;

    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

    /**
     * mount function
     */
    public function mount()
    {
        // 対象年月を設定
        // セッション変数にキー（workYear、workMonth）が設定されている場合は、その値を取得
        if (session()->has(AppConsts::SESS_WORK_YEAR)) {
            $this->workYear = session(AppConsts::SESS_WORK_YEAR);
        } else {
            $this->workYear = date('Y');
            session([AppConsts::SESS_WORK_YEAR => $this->workYear]);
        }
        if(session()->has(AppConsts::SESS_WORK_MONTH)) {
            $this->workMonth = session(AppConsts::SESS_WORK_MONTH);
        } else {
            $this->workMonth = date('m');
            $Day = date('d');
            if ($Day < 15) {
                $this->workYear = date('Y', strtotime('-1 month'));
                $this->workMonth = date('m', strtotime('-1 month'));
            }
            session([AppConsts::SESS_WORK_MONTH => $this->workMonth]);
        }
        
        if(session()->has(AppConsts::SESS_CLIENT_ID)) {
            $this->client_id = session(AppConsts::SESS_CLIENT_ID);
        } else {
            $this->client_id = null;
        }if(session()->has(AppConsts::SESS_CLIENT_PLACE_ID)) {
            $this->clientplace_id = session(AppConsts::SESS_CLIENT_PLACE_ID);
        } else {
            $this->clientplace_id = null;
        }

        $this->refClients = modelClients::all();
        $this->refClientPlaces = [];
    }

    /**
     * render function
     */
    public function render()
    {
        $query = modelBills::with('client', 'clientplace')
            ->select('bills.*')
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->join('clientplaces', 'bills.clientplace_id', '=', 'clientplaces.id')
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth);

        if ($this->client_id) {
            $query->where('bills.client_id', $this->client_id);
            $this->refClientPlaces = modelClientPlaces::where('client_id', $this->client_id)->get();
        }

        if($this->clientplace_id) {
            $query->where('bills.clientplace_id', $this->clientplace_id);
        }

        $Bills = $query->paginate(10);

        return view('livewire.bills', compact('Bills'));
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->validate();
        session([AppConsts::SESS_WORK_YEAR => $this->workYear]);
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->validate();
        session([AppConsts::SESS_WORK_MONTH => $this->workMonth]);
    }

    /**
     * client_id updated
     * */
    public function updateClientId($value)
    {
        // client_idが更新されたときに呼び出される
        $this->refClientPlaces = modelClientPlaces::where('client_id', $value)->get(); // 新しいclient_idに基づいて場所のデータを取得
        $this->clientplace_id = null; // clientplace_idをリセット
    }

    /**
     * 部門IDが変更されたときに呼び出される
     * @param int $clientplace_id
     * @return void
     * 参照部門を設定する
     */
    public function updateClientplaceId($clientplace_id)
    {
        if($clientplace_id)
        {
            $this->selectedClientPlace = modelClientPlaces::find($clientplace_id);
        }
        else
        {
            $this->selectedClientPlace = null;
        }
    }

    /**
     * redirect to bill details page 
     * */
    public function showBillDetails($billId)
    {
        return redirect()->route('billdetails',
            ['billId' => $billId]);
    }
}
