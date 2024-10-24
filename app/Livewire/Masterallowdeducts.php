<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\masterallowdeducts as modelMad;

class Masterallowdeducts extends Component
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
     * delete action listener
     */
    protected $listeners = [
        'deleteMadListener' => 'deleteMad'
    ];

    /**
     * List of add/edit form validation rules
     */
    protected $rules = [
        'mad_cd' => 'required',
        'mad_name' => 'required',
    ];

    public function render()
    {
        $this->Mads = modelMad::select('id', 'mad_cd', 'mad_allow', 'mad_deduct', 'mad_name', 'mad_notes')->get();
        return view('livewire.masterallowdeducts');
    }

    /**
     * Open Add Mad form
     * @return void
     */
    public function newMad()
    {
        return redirect()->route('allowdeductcreate');
    }

    /**
     * Open Add Mad form
     * @return void
     */
    public function editMad($id)
    {
        return redirect()->route('allowdeductupdate', ['id' => $id]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteMad($id) {
        try {
            modelMad::where('id', $id)->delete();
            session()->flash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
