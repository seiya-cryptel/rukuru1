<?php

namespace App\Livewire;

use Livewire\Component;

class Closesalaries extends Component
{
    /**
     * work year, month
     * */
    public $workYear, $workMonth;

    /**
     * closing is enabled or not
     * true if closing is enabled otherwise reopen is enabled
     */
    public $isClose = true;

    /**
     * enable close button
     * true if enable close button
     */
    public $enableCloseButton = true;

    /**
     * rules for validation
     */
    protected $rules = [
        'workYear' => 'required',
        'workMonth' => 'required',
    ];

    /**
     * set isClose
     */
    protected function setIsClose()
    {
        // closepayrolls テーブルに対象年月のレコードが存在するか確認
        $closepayrolls = modelClosePayrolls::where('work_year', $this->workYear)
            ->where('work_month', $this->workMonth)
            ->first();
        // 存在する場合は、closepayrolls テーブルの closed カラムの値を取得
        // closed カラムの値が true の場合は、isClose に false を設定
        $this->isClose = $closepayrolls ? ($closepayrolls->closed ? false : true) : true;        
    }
    
    /**
     * mount function
     */
    public function mount()
    {
        // 対象年月の初期設定
        // 日にちが < 15 の場合は、前月の年月を設定
        $this->workYear = date('Y');
        $this->workMonth = date('m');
        $Day = date('d');
        if ($Day < 15) {
            $this->workYear = date('Y', strtotime('-1 month'));
            $this->workMonth = date('m', strtotime('-1 month'));
        }

        // close or open の設定
        $this->setIsClose();
    }

    /**
     * render function
     */
    public function render()
    {
        return view('livewire.closesalaries');
    }

    /**
     * 対象年が変更された場合の処理
     */
    public function changeWorkYear($value)
    {
        $this->enableCloseButton = false;
        $this->validate();
        $this->setIsClose();
        $this->enableCloseButton = true;
    }

    /**
     * 対象月が変更された場合の処理
     */
    public function changeWorkMonth()
    {
        $this->enableCloseButton = false;
        $this->validate();
        $this->setIsClose();
        $this->enableCloseButton = true;
    }

    /**
     * 給与締め処理
     */
    public function closeSalaries()
    {

    }

    /**
     * 給与締め解除処理
     */
    public function reopenSalaries()
    {

    }
}
