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
    public function pricetables()
    {
        return $this->hasMany(pricetables::class);
    }
    public function bills()
    {
        return $this->hasMany(bills::class);
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
}
