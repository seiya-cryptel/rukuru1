<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\applogs;
use App\Models\masterallowdeducrs;

abstract class AllowdeductBase extends Component
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
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'mad_cd.required' => __('Required'),
            'mad_name.required' => __('Required'),
        ];
    }

    /**
     * render the view
     */
    abstract public function render();

    /**
     * Cancel add/edit form and redirect to the master list
     * @return void
     */
    public function cancelMad() {
        return redirect()->route('masterallowdeduct');
    }
}
