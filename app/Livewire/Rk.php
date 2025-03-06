<?php

namespace App\Livewire;

use Livewire\WithPagination;

use Livewire\Component;

use App\Consts\AppConsts;
use App\Services\PhpSpreadsheetService;

use App\Models\employeeworks as employeeworks;
use App\Models\clients as clients;
use App\Models\employees as employees;

class Rk extends Component
{
    use WithPagination;

    /**
     * 表示条件
     */
    public $dateFrom;
    public $dateTo;
    public $client_id;
    public $emplCdFrom;
    public $emplCdTo;

    /**
     * rules
     */
    protected $rules = [
        'dateFrom' => 'required|date',
        'dateTo' => 'required|date',
    ];

    /**
     * 勤怠明細クエリを作成する
     */
    protected function makeQuery()
    {
        $Query = employeeworks::with('client')->with('employee')
            ->join('employees as employee', 'employee.id', '=', 'employeeworks.employee_id')
            ->join('clients as client', 'client.id', '=', 'employeeworks.client_id')
            ->select('employeeworks.*', 'client.cl_cd', 'client.cl_name', 'employee.empl_cd', 'employee.empl_name_last', 'employee.empl_name_first')
            ->whereBetween('employeeworks.wrk_date', [$this->dateFrom, $this->dateTo]);

        // 顧客条件
        if ($this->client_id) {
            $Query->where('employeeworks.client_id', $this->client_id);
        }

        // 従業員条件
        if ($this->emplCdFrom) {
            if ($this->emplCdTo) {
                $Query->whereBetween('employee.empl_cd', [$this->emplCdFrom, $this->emplCdTo]);
            } else {
                $Query->where('employee.empl_cd', '>=', $this->emplCdFrom);
            }
        }
        else if ($this->emplCdTo) {
            $Query->where('employee.empl_cd', '<=', $this->emplCdTo);
        }

        $Query->orderBy('employee.empl_cd')
            ->orderBy('employeeworks.wrk_date')
            ->orderBy('employeeworks.wrk_seq');
        return $Query;
    }

    /**
     * mount
     */
    public function mount()
    {
        $nCur = strtotime(date('Y-m-1'));
        $nFrom = strtotime('-1 Month', $nCur);
        $nTo = strtotime('-1 Day', strtotime('+1 Month', $nFrom));
        $this->dateFrom = date('Y-m-d', $nFrom);
        $this->dateTo = date('Y-m-d', $nTo);
        $this->emplCdFrom = '';
        $this->emplCdTo = '';
        $this->clientId = '';
    }

    public function render()
    {
        $Query = $this->makeQuery();
        $Query->limit(1000);
        $Employeeworks = $Query->paginate(AppConsts::PAGINATION);

        $Clients = clients::orderBy('cl_cd')->get();
        $Employees = employees::orderBy('empl_cd')->get();

        return view('livewire.rk', compact('Employeeworks', 'Clients', 'Employees'));
    }

    /**
     * 期間開始日変更
     */
    public function changeDateFrom($value)
    {
        $this->dateFrom = $value;
    }

    /**
     * 期間終了日変更
     */
    public function changeDateTo($value)
    {
        $this->dateTo = $value;
    }

    /**
     * 顧客変更
     */
    public function changeClient($value)
    {
        $this->client_id = $value;
    }

    /**
     * 従業員コードFrom変更
     */
    public function changeEmployeeFrom($value)
    {
        $this->emplCdFrom = $value;
    }

    /**
     * 従業員コードTo変更
     */
    public function changeEmployeeTo($value)
    {
        $this->emplCdTo = $value;
    }

    /**
     * Excel出力
     */
    public function exportExcel()
    {
        $service = new PhpSpreadsheetService();
        $Query = $this->makeQuery();
        $Employeeworks = $Query->get();

        return $service->exportKintaiDetail(
            storage_path('data/kintai_detail_template.xlsx'),
            [
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
                'client_id' => $this->client_id,
                'empl_cd_from' => $this->emplCdFrom,
                'empl_cd_to' => $this->emplCdTo,
            ],
            $Employeeworks
        );
    }
}
