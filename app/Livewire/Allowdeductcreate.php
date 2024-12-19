<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\masterallowdeducts as modelMad;

class Allowdeductcreate extends Component
{
    /**
     * record set of master allow deducts
     * */
    public $Mads;
    /**
     * master allow deducts fields
     */
    public $mad_cd, $mad_allow, $mad_deduct, $mad_name, $mad_notes;
    /**
     * master allow deducts id and mode flags
     */
    public $madId, $updateMad = false, $addMad = false;

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'mad_cd' => 'required',
        'mad_name' => 'required',
    ];

    /**
     * Reseting all the input fields
     * @return void
     */
    public function resetFields()
    {
        $this->mad_cd = '';
        $this->mad_allow = false;
        $this->mad_deduct = false;
        $this->mad_name = '';
        $this->mad_notes = '';
    }

    public function render()
    {
        $this->resetFields();
        return view('livewire.allowdeductcreate');
    }

    /**
     * store the master input post data in the master table
     * @return void
     */
    public function storeMad()
    {
        $this->validate();
        try {
            modelMad::create([
                'mad_cd' => $this->mad_cd,
                'mad_allow' => $this->mad_allow,
                'mad_deduct' => $this->mad_deduct,
                'mad_name' => $this->mad_name,
                'mad_notes' => $this->mad_notes
            ]);
            session()->flash('success', __('Save') . __('Done'));
            return redirect()->route('masterallowdeduct');
        } catch (\Exception $e) {
            session()->flash('error', __('Something went wrong.'));
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelMad() {
        return redirect()->route('masterallowdeduct');
    }
}
