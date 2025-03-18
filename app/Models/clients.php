<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
    use HasFactory;

    /**
     * Relationship with client places
     */
    public function clientplaces()
    {
        return $this->hasMany(clientplaces::class);
    }
    public function bills()
    {
        return $this->hasMany(bills::class);
    }
    public function employees()
    {
        return $this->hasMany(employees::class);
    }
    public function employeeallowdeducts()
    {
        return $this->hasMany(employeeallowdeduct::class);
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cl_cd',
        'cl_name',
        'cl_full_name',
        'cl_kana',
        'cl_alpha',
        'cl_zip',
        'cl_addr1',
        'cl_addr2',
        'cl_psn_div',
        'cl_psn_title',
        'cl_psn_name',
        'cl_psn_kana',
        'cl_psn_mail',
        'cl_psn_tel',
        'cl_psn_fax',
        'cl_dow_statutory',
        'cl_dow_non_statutory',
        'cl_over_40hpw',
        'cl_dow_first',
        'cl_round_start',
        'cl_round_end',
        'cl_close_day',
        'cl_kintai_style',
        'cl_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        ];
    }

    /**
     * 顧客の休日判定
     * @param string $date 日付
     * return 1: 法定休日 2: 法定外休日 0: 平日
     */
    public function typeOfHoliday($date)
    {
        $dayOfWeek = date('w', strtotime($date));

        // 顧客の休日判定
        if($dayOfWeek == $this->cl_dow_statutory)
        {
            // 法定休日
            return 1;
        }
        if(($this->cl_dow_non_statutory != null) && ($dayOfWeek == $this->cl_dow_non_statutory))
        {
            // 法定外休日
            return 2;
        }

        return 0;
    }
}
