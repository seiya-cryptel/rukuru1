<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\holiday as modelHoliday;
use App\Models\clients as modelClients;

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
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
        $holiday = modelHoliday::find($id);
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
            modelHoliday::find($this->holidayId)->update([
                'holiday_date' => $this->holiday_date,
                'client_id' => $this->client_id,
                'holiday_name' => $this->holiday_name,
                'notes' => $this->notes,
            ]);
            return redirect()->route('holiday');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
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
