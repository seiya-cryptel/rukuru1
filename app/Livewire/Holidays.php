<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;

use App\Models\holiday as modelHoliday;
use App\Models\clients as modelClients;

class Holidays extends Component
{
    use WithPagination;

    /**
     * search year
     */
    public $targetYear = '';

    /**
     * reference to client records
     */
    public $refClients;

    /**
     * delete action listener
     */
    protected $listeners = [
        'deleteHolidayListener' => 'deleteHoliday',
    ];

    /**
     * mount the component
     */
    public function mount()
    {
        if (session()->has(AppConsts::SESS_WORK_YEAR)) {
            $this->targetYear = session()->get(AppConsts::SESS_WORK_YEAR);
        } else {
            $this->targetYear = date('Y');
            session()->put(AppConsts::SESS_WORK_YEAR, $this->targetYear);
        }
    }

    /**
     * render the livewire component
     */
    public function render()
    {
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
        $Holidays = modelHoliday::whereYear('holiday_date', $this->targetYear)
            ->orderBy('holiday_date', 'asc')
            ->paginate(AppConsts::PAGINATION);
        return view('livewire.holidays', compact('Holidays'));
    }

    /**
     * change the target year
     */
    public function changeYear($year)
    {
        $this->targetYear = $year;
        session()->put(AppConsts::SESS_WORK_YEAR, $this->targetYear);
    }

    /**
     * add a new holiday
     */
    public function newHoliday()
    {
        return redirect()->route('holidaycreate');
    }

    /**
     * edit a holiday
     */
    public function editHoliday($id)
    {
        return redirect()->route('holidayupdate', ['id' => $id]);
    }

    /**
     * delete a holiday
     */
    public function deleteHoliday($id)
    {
        modelHoliday::destroy($id);
    }
}
