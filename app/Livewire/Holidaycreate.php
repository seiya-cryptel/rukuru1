<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\holiday as modelHoliday;
use App\Models\clients as modelClients;

class Holidaycreate extends Component
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
     * List of add/edit form validation rules
     */
    protected $rules = [
        'holiday_date' => 'required',
        'client_id' => 'required',
        'holiday_name' => 'required',
    ];

    /**
     * reseting all the input fields
     */
    public function resetFields()
    {
        $this->holiday_date = '';
        $this->client_id = 0;
        $this->holiday_name = '';
        $this->notes = '';
    }

    public function render()
    {
        $this->refClients = modelClients::orderBy('cl_name', 'asc')->get();
        $this->resetFields();
        return view('livewire.holidaycreate');
    }

    /**
     * store a new holiday
     */
    public function storeHoliday()
    {
        $this->validate();
        try {
            modelHoliday::create([
                'holiday_date' => $this->holiday_date,
                'client_id' => $this->client_id,
                'holiday_name' => $this->holiday_name,
                'notes' => $this->notes,
            ]);
            session()->flash('success', __('Create'). ' ' . __('Done'));
            return redirect()->route('holiday');
        } catch (\Exception $e) {
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
