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
        $this->Mads = modelMad::select('id', 'mad_cd', 'mad_allow', 'mad_deduct', 'mad_name', 'mad_notes')->get();
        return view('livewire.masterallowdeducts');
    }

    /**
     * Open Add Mad form
     * @return void
     */
    public function newMad()
    {
        $this->resetFields();
        $this->addMad = true;
        $this->updateMad = false;
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
            $this->resetFields();
            $this->addMad = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong, please try again later.');
        }
    }

    /**
     * show existing master data in the edit form
     * @param mixed $id
     * @return void
     */
    public function editMad($id) {
        try {
            $Mad = modelMad::findOrFail($id);
            if(!$Mad) {
                session()->flash('error', 'Master record not found.');
            }
            else {
                $this->madId = $id;
                $this->mad_cd = $Mad->mad_cd;
                $this->mad_allow = $Mad->mad_alloe;
                $this->mad_deduct = $Mad->mad_deduct;
                $this->mad_name = $Mad->mad_name;
                $this->mad_notes = $Mad->mad_notes;

                $this->updateMad = true;
                $this->addMad = false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * update the master data
     * @return void
     */
    public function updateMad() {
        $this->validate();
        try {
            modelMad::where('id', $this->madId)->update([
                'mad_cd' => $this->mad_cd,
                'mad_allow' => $this->mad_allow,
                'mad_deduct' => $this->mad_deduct,
                'mad_name' => $this->mad_name,
                'mad_notes' => $this->mad_notes
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelMad() {
        $this->resetFields();
        $this->addMad = false;
        $this->updateMad = false;
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
