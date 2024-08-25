<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\applogs as modelApplogs;

class Applogs extends Component
{
    use WithPagination;

    /**
     * applogs records
     */
    // public $Applogs;

    /**
     * render the applogs component
     */
    public function render()
    {
        $Applogs = modelApplogs::orderBy('logged_at', 'desc')
            ->take(100)
            ->paginate(10);
        return view('livewire.applogs', compact('Applogs'));
    }

    /**
     * return log type string
     */
    public static function logTypeString(int $logType): string
    {
        return modelApplogs::LOG_MESSAGES[$logType] ?? '';
    }
}
