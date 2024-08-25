<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;

use App\Models\notice as modelNotice;

class Notices extends Component
{
    /**
     * Notice record set
     */
    public $Notices;

    /**
     * render the component
     */
    public function render()
    {
        $this->Notices = modelNotice::orderBy('notice_date', 'desc')
            ->take(3)
            ->get();
        return view('livewire.notices');
    }
}
