<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

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
        // set default values
        // 対象年月を設定
        // セッション変数にキー（workYear、workMonth）が設定されている場合は、その値を取得
        // 値を取得したあとは、セッション変数を削除
        if (session()->has('workYear')) {
            $this->workYear = session('workYear');
            session()->forget('workYear');
        } else {
            $this->workYear = date('Y');
        }
        if(session()->has('workMonth')) {
            $this->workMonth = session('workMonth');
            session()->forget('workMonth');
        } else {
            $this->workMonth = date('m');
            $Day = date('d');
            if ($Day < 15) {
                $this->workYear = date('Y', strtotime('-1 month'));
                $this->workMonth = date('m', strtotime('-1 month'));
            }
        }
        if(session()->has('client_id')) {
            $this->client_id = session('client_id');
            session()->forget('client_id');
        } else {
            $this->client_id = null;
        }if(session()->has('clientplace_id')) {
            $this->clientplace_id = session('clientplace_id');
            session()->forget('clientplace_id');
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
            ->join('clients', 'bills.client_id', '=', 'clients.id')
            ->join('clientplaces', 'bills.clientplace_id', '=', 'clientplaces.id')
            ->where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth);

        if ($this->client_id) {
            $query->where('client_id', $this->client_id);
            $this->refClientPlaces = modelClientPlaces::where('client_id', $this->client_id)->get();
        }

        if($this->clientplace_id) {
            $query->where('clientplace_id', $this->clientplace_id);
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
        session(['workYear' => $this->workYear]);
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->validate();
        session(['workMonth' => $this->workMonth]);
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
     * redirect to bill details page 
     * */
    public function showBillDetails($billId)
    {
        // セッション変数にキー（bill_id）を設定
        session(['bill_id' => $billId]);
        return redirect()->route('billdetails');
    }
}
