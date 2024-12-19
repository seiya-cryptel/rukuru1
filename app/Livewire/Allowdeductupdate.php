<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\masterallowdeducts as modelMad;

class Allowdeductupdate extends Component
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
     * mount the component
     */
    public function mount($id)
    {
        $mad = modelMad::find($id);
        $this->madId = $id;
        $this->mad_cd = $mad->mad_cd;
        $this->mad_allow = $mad->mad_allow;
        $this->mad_deduct = $mad->mad_deduct;
        $this->mad_name = $mad->mad_name;
        $this->mad_notes = $mad->mad_notes;
    }

    public function render()
    {
        return view('livewire.allowdeductupdate');
    }

    /**
     * update the master data
     * @return void
     */
    public function updateMad2() {
        $this->validate();
        try {
            modelMad::where('id', $this->madId)->update([
                'mad_cd' => $this->mad_cd,
                'mad_allow' => $this->mad_allow,
                'mad_deduct' => $this->mad_deduct,
                'mad_name' => $this->mad_name,
                'mad_notes' => $this->mad_notes
            ]);
            session()->flash('success', __('Save') . __('Done'));
            return redirect()->route('masterallowdeduct');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
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
