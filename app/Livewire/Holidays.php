<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Consts\AppConsts;
use App\Models\applogs;
use App\Models\holiday;
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
        $Holidays = holiday::whereYear('holiday_date', $this->targetYear)
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
        // locale is passed as a parameter to pass the test
        return redirect()->route('holidaycreate', ['locale' => app()->getLocale()]);
    }

    /**
     * edit a holiday
     */
    public function editHoliday($id)
    {
        return redirect()->route('holidayupdate', ['id' => $id, 'locale' => app()->getLocale()]);
    }

    /**
     * delete a holiday
     */
    public function deleteHoliday($id)
    {
        try {
            holiday::destroy($id);
            $logMessage = '祝日マスタ 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_HOLIDAY, $logMessage);
            session()->flash('success', __('Holiday deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '祝日マスタ 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }
}
