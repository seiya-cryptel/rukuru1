<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\holiday;
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
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'holiday_date.required' => '必須項目',
            'holiday_name.required' => '必須項目',
        ];
    }

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
            holiday::create([
                'holiday_date' => $this->holiday_date,
                'client_id' => $this->client_id,
                'holiday_name' => $this->holiday_name,
                'notes' => $this->notes,
            ]);
            $logMessage = '祝日マスタ 作成: ' . $this->holiday_name . ' 顧客ID ' . $this->client_id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_HOLIDAY, $logMessage);
            session()->flash('success', __('Holiday created successfully.'));
            return redirect()->route('holiday');
        } catch (\Exception $e) {
            $logMessage = '祝日マスタ 作成 エラー: ' . $e->getMessage();
            logger($logMessage);
            applogs::insertLog(applogs::LOG_ERROR, $logMessage);
            session()->flash('error', $logMessage);
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
