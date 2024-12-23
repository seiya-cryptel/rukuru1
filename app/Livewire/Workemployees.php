<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\closepayrolls as modelClosePayrolls;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\employees as modelEmployees;
use App\Models\employeepays as modelEmployeePays;       // 従業員単価
use App\Models\employeeworks as modelEmployeeWorkss;    // 勤怠データ

/**
 * Workemployees class
 * 
 * 勤怠入力用の従業員一覧画面
 * 
 * @category Livewire component
 * @package  App\Livewire
 */
class Workemployees extends Component
{
    use WithPagination;

    /**
     * work year, month and client information
     * */
    public $workYear, $workMonth, $client_id, $clientplace_id;

    /**
     * search string for employees
     */
    public $search = '';

    /**
     * records for select box
     * */
    public $refClients;
    public $refClientPlaces = [];

    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
        'client_id' => 'required',
        'clientplace_id' => 'required',
    ];

    /**
     * mount function
     * */
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

        // 従業員検索条件を取得
        if(session()->has(AppConsts::SESS_SEARCH)) {
            $this->search = session(AppConsts::SESS_SEARCH);
        } else {
            $this->search = '';
        }

        // if client_id or clientplace_id is null then show guide message
        if($this->client_id == null || $this->clientplace_id == null)
        {
            session()->flash('success', __('Client') . 'と' . __('Work Place') . 'を選択してください。');
        }

        $this->refClients = modelClients::all();
        if($this->client_id != null)
        {
            $this->refClientPlaces = modelClientPlaces::where('client_id', $this->client_id)->get();
        }
    }

    /**
     * render function
     * */
    public function render()
    {
        // 勤怠対象月の初日と最終日を取得
        $firstDay = date('Y-m-01', strtotime($this->workYear.'-'.$this->workMonth.'-01'));
        $lastDay = date('Y-m-t', strtotime($this->workYear.'-'.$this->workMonth.'-01'));

        $query = modelEmployees::where(function ($query) {
            $query->where('empl_name_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_name_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_kana_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_last', 'like', '%'.$this->search.'%')
            ->orWhere('empl_alpha_first', 'like', '%'.$this->search.'%')
            ->orWhere('empl_email', 'like', '%'.$this->search.'%')
            ->orWhere('empl_mobile', 'like', '%'.$this->search.'%')
            ->orWhere('empl_cd', 'like', '%'.$this->search.'%')
            ->orWhere('empl_notes', 'like', '%'.$this->search.'%')
            ;
        });

        $query = $query->where('empl_hire_date', '<=', $lastDay);

        $query = $query->where(function ($query) use ($firstDay) {
            $query->where('empl_resign_date', '>=', $firstDay)
            ->orWhere('empl_resign_date', null)
            ->orWhere('empl_resign_date', '0000-00-00 00:00:00')
            ->orWhere('empl_resign_date', '');
        });

        $Employees = $query->paginate(AppConsts::PAGINATION);
            
        return view('livewire.workemployees', compact('Employees'));
    }

    /**
     * change search keyword
     */
    public function changeSearch()
    {
        session([AppConsts::SESS_SEARCH => $this->search]);
    }

    /**
     * clear search keyword
     */
    public function clearSearch()
    {
        $this->search = '';
        session([AppConsts::SESS_SEARCH => '']);
    }

    /**
     * client_id updated
     * */
    public function updateClientId($value)
    {
        // client_idが更新されたときに呼び出される
        $this->refClientPlaces = modelClientPlaces::where('client_id', $value)->get(); // 新しいclient_idに基づいて場所のデータを取得
        $this->clientplace_id = null; // clientplace_idをリセット
        session([AppConsts::SESS_CLIENT_ID => $value]);
        session([AppConsts::SESS_CLIENT_PLACE_ID => null]);
    }

    /**
     * clientplace_id updated
     * */
    public function updateClientplaceId($value)
    {
        // clientplace_idが更新されたときに呼び出される
        $this->clientplace_id = $value;
        session([AppConsts::SESS_CLIENT_PLACE_ID => $value]);
    }

    /**
     * work year updated
     */
    public function updateWorkYear($value)
    {
        session([AppConsts::SESS_WORK_YEAR => $value]);
    }

    /**
     * work month updated
     */
    public function updateWorkMonth($value)
    {
        session([AppConsts::SESS_WORK_MONTH => $value]);
    }

    /**
     * timekeeping record exists or not
     * */
    public function timekeepingExists($employeeId)
    {
        try {
            $this->validate();
        } catch (\Exception $e) {
            return 'error';
        }

        // 勤怠データが存在するかどうかを確認する
        $firstDay = date('Y-m-01', strtotime($this->workYear.'-'.$this->workMonth.'-01'));
        $lastDay = date('Y-m-t', strtotime($this->workYear.'-'.$this->workMonth.'-01'));

        $Query = modelEmployeeWorkss::where('employee_id', $employeeId)
            ->where('client_id', $this->client_id)
            ->where('clientplace_id', $this->clientplace_id)
            ->whereBetween('wrk_date',[$firstDay, $lastDay]);
        return $Query->exists() ? 'exists' : 'notexists';
    }

    /**
     * redirect to timekeeping edit page 
     * */
    public function editTimekeeping($employeeId)
    {
        return redirect()->route('employeework', 
            ['workYear' => $this->workYear, 'workMonth' => $this->workMonth, 
            'clientId' => $this->client_id, 'clientPlaceId' => $this->clientplace_id, 'employeeId' => $employeeId]);
    }
}
