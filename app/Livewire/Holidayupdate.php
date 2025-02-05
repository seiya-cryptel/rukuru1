<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\holiday;
use App\Models\clients;

class Holidayupdate extends Component
{

    /**
     * reference to client records
     */
    public $refClients;

    /**
     * editing atributes
     */
    public $holiday_date, $client_id, $holiday_name, $notes;
    /**
     * editing id and mode flags
     */
    public $holidayId;

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'holiday_date' => 'required',
        'client_id' => 'required',
        'holiday_name' => 'required',
    ];

    /**
     * mount the component
     */
    public function mount($id)
    {
        $this->refClients = clients::orderBy('cl_name', 'asc')->get();
        $holiday = holiday::find($id);
        $this->holidayId = $id;
        $this->holiday_date = $holiday->holiday_date;
        $this->client_id = $holiday->client_id;
        $this->holiday_name = $holiday->holiday_name;
        $this->notes = $holiday->notes;
    }

    public function render()
    {
        return view('livewire.holidayupdate');
    }

    /**
     * update a holiday
     */
    public function updateHoliday2()
    {
        $this->validate();
        try {
            holiday::find($this->holidayId)->update([
                'holiday_date' => $this->holiday_date,
                'client_id' => $this->client_id,
                'holiday_name' => $this->holiday_name,
                'notes' => $this->notes,
            ]);
            $logMessage = '祝日マスタ 更新: ' . $this->holiday_name . ' 顧客ID ' . $this->client_id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_HOLIDAY, $logMessage);
            session()->flash('success', __('Update'). ' ' . __('Done'));
            return redirect()->route('holiday');
        } catch (\Exception $e) {
            $logMessage = '祝日マスタ 更新エラー: ' . $e->getMessage();
            logger(logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * cancel add/edit form
     */
    public function cancelHoliday()
    {
        return redirect()->route('holiday');
    }
}
