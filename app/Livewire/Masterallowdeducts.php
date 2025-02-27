<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;

use App\Models\applogs;
use App\Models\masterallowdeducts as modelMad;

class Masterallowdeducts extends Component
{
    use WithPagination;

    /**
     * record set of master allow deducts
     * */
    // public $Mads;
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
        $Mads = modelMad::orderBy('mad_cd')
            ->paginate(15);
        return view('livewire.masterallowdeducts', compact('Mads'));
    }

    /**
     * Open Add Mad form
     * @return void
     */
    public function newMad()
    {
        return redirect()->route('allowdeductcreate', ['locale' => app()->getLocale()]);
    }

    /**
     * Open Add Mad form
     * @return void
     */
    public function editMad($id)
    {
        return redirect()->route('allowdeductupdate', ['locale' => app()->getLocale(), 'id' => $id]);
    }

    /**
     * delete specific master data
     * @param mixed $id
     * @return void
     */
    public function deleteMad($id) {
        try {
            modelMad::destroy($id);
            $logMessage = '手当控除 削除: ' . $id;
            logger($logMessage);
            applogs::insertLog(applogs::LOG_TYPE_MASTER_ALLOWDEDUCT, $logMessage);
            session()->flash('success', __('Allow Deduct deleted successfully.'));
        } catch (\Exception $e) {
            $logMessage = '手当控除 削除 エラー: ' . $e->getMessage();
            logger($logMessage);
            session()->flash('error', $logMessage);
        }
    }
}
