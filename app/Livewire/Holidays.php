<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\holiday as modelHoliday;
use App\Models\clients as modelClients;

class Holidays extends Component
{
    use WithPagination;

    const SESSION_TARGET_YEAR = __CLASS__ . '::targetYear';
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
        $this->targetYear = session()->has(self::SESSION_TARGET_YEAR) ? session()->get(self::SESSION_TARGET_YEAR) : date('Y');
    }

    /**
     * render the livewire component
     */
    public function render()
    {
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
        $Holidays = modelHoliday::whereYear('holiday_date', $this->targetYear)
            ->orderBy('holiday_date', 'asc')
            ->paginate(25);
        session()->put(self::SESSION_TARGET_YEAR, $this->targetYear);
        return view('livewire.holidays', compact('Holidays'));
    }

    /**
     * change the target year
     */
    public function changeYear($year)
    {
        $this->targetYear = $year;
        session()->put(self::SESSION_TARGET_YEAR, $this->targetYear);
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
