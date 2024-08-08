<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\clients as modelClients;
use App\Models\clientplaces as modelClientPlaces;
use App\Models\clientworktypes as modelClientWorktypes;
use App\Models\employees as modelEmployees;
use App\Models\employeepays as modelEmployeePays;

class Employeepays extends Component
{
    /**
     * record set of table client for select box
     * */
    public $refClients;

    /**
     * record set of table client places for select box
     * */
    public $vrefClientPlaces = [];

    /**
     * record set of table worktypes for select box
     * */
    public $vrefClientWorktypes = [];

    /**
     * record array of employee pays
     * */
    public $vEmployeepays=[];

    /**
     * record set of master allow deducts
     * */
    protected $employeepays;

    /**
     * Employee attributes
     */
    public $empl_cd, $empl_name_last, $empl_name_first;

    /**
     * field values of employee pays
     */
    public $employee_id, $client_id, $clientplace_id, $wt_cd, $payhour, $billhour;
    
    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'vEmployeepays.*.wt_cd' => 'required',
    ];

    /**
     * validation rules attributes
     */
    protected $validationAttributes = [
        'vEmployeepays.*.wt_cd' => '作業種別',
    ];

    /**
     * work type records by client id and client place id
     */
    protected function getWorktypes($client_id, $clientplace_id)
    {
        if(empty($client_id) || is_null($client_id))
        {
            $query = modelClientWorktypes::wherenull('client_id');
        }
        else
        {
            $query = modelClientWorktypes::wherenull('client_id')
                ->orWhere('client_id', $client_id);
        }
        if(empty($clientplace_id) || is_null($clientplace_id))
        {
            $query = $query->wherenull('clientplace_id');
        }
        else
        {
            $query = $query->wherenull('clientplace_id')
                ->orWhere('clientplace_id', $clientplace_id);
        }
        return $query->get();
    }
    
    /**
     * mount function
     */
    public function mount($employee_id)
    {
        $this->refClients = modelClients::select('id', 'cl_cd', 'cl_name', 'cl_kana', 'cl_alpha')->get();
 
        $employee = modelEmployees::find($employee_id);
        $this->empl_cd = $employee->empl_cd;
        $this->empl_name_last = $employee->empl_name_last;
        $this->empl_name_first = $employee->empl_name_first;

        $this->employee_id = $employee_id;
        $res = modelEmployeePays::where('employee_id', $employee_id);
        if($res)
        {
            $this->employeepays = $res->get();
            $this->vEmployeepays = $this->employeepays->toArray();
        }
        else
        {
            $record = modelEmployeePays::create([
                'employee_id' => $employee_id,
                'client_id' => null,
                'clientplace_id' => null,
                'wt_cd' => 'N',
                'payhour' => '0',
                'billhour' => '0',
            ]);
            $this->vEmployeepays[] = $record->toArray();
        }

        // setup worktypes array for select box
        foreach($this->vEmployeepays as $key => $employeepay)
        {
            $this->vrefClientWorktypes[$key] = $this->getWorktypes($employeepay['client_id'], $employeepay['clientplace_id']);
        }
    }

    /**
     * updateClientId function
     */
    public function updateClientId($value, $key)
    {
        // client_idが更新されたときに呼び出される
        $this->vrefClientPlaces[$key] = modelClientPlaces::where('client_id', $value)->get(); // 新しいclient_idに基づいて場所のデータを取得
        $this->vrefClientWorktypes[$key] = $this->getWorktypes($this->vEmployeepays[$key]['client_id'], $this->vEmployeepays[$key]['clientplace_id']);
        $this->vEmployeepays[$key]['clientplace_id'] = null; // clientplace_idをリセット
        $this->vEmployeepays[$key]['work_cd'] = null;
    }

    /**
     * updateClientPlaceId function
     */
    public function updateClientPlaceId($value, $key)
    {
        // clientplace_idが更新されたときに呼び出される
        $this->vrefClientWorktypes[$key] = $this->getWorktypes($this->vEmployeepays[$key]['client_id'], $this->vEmployeepays[$key]['clientplace_id']);
        $this->vEmployeepays[$key]['work_cd'] = null;
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.employeepays');
    }

    /**
     * Add a new employee pay record to the list
     * @return void
     */
    public function newEmployeepays()
    {
        $this->vEmployeepays[] = [
            'client_id' => null,
            'clientplace_id' => null,
            'wt_cd' => 'N',
            'payhour' => '0',
            'billhour' => '0',
        ];
    }

    /**
     * Remove an employee pay record from the list
     * @param int $index
     * @return void
     */
    public function removeEmployeepays($index)
    {
        unset($this->vEmployeepays[$index]);
        // $this->vEmployeepays = array_values($this->vEmployeepays);
    }

    public function saveEmployeepays()
    {
        $this->validate();
        try {
            foreach($this->vEmployeepays as $employeepay)
            {
                if($employeepay['id'])
                {
                    modelEmployeePays::find($employeepay['id'])->update($employeepay);
                }
                else
                {
                    modelEmployeePays::create($employeepay);
                }
            }
            session()->flash('success', 'Employee pay records saved successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelEmployeepay() {
        return redirect()->route('employee');
    }
}
