<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\clients as modelClient;
use App\Models\clientplaces as modelClientPlace;
use App\Models\bills as modelBill;
use App\Models\billdetails as modelBillDetails;

class Billdetails extends Component
{
    // parameters
    public $bill_id;

    // related records
    public $Bill;
    public $Client;
    public $ClientPlace;
    public $BillDetails;

    /**
     * mount function
     */
    public function mount()
    {
        // セッション変数から取得する
        $this->bill_id = session('bill_id');
        // セッション変数を削除する
        session()->forget('bill_id');
        // 関連レコードを取得
        $this->Bill = modelBill::find($this->bill_id);
        $this->Client = $this->Bill->client;
        $this->ClientPlace = $this->Bill->clientplace;
        // 請求明細情報を取得
        $this->BillDetails = modelBillDetails::where('bill_id', $this->bill_id)->get();
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.billdetails');
    }

    /**
     * cancel bill details
     */
    public function cancelBillDetails()
    {
        // セッション変数にキーを設定する
        session(['workYear' => $this->Bill->workYear]);
        session(['workMonth' => $this->Bill->workMonth]);
        session(['client_id' => $this->Bill->client_id]);
        session(['clientplace_id' => $this->Bill->clientplace_id]);

        // redirect to workemployees
        return redirect()->route('bills');
    }
}
