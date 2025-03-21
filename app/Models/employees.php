<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

// use App\Models\clients;
// use App\Models\clientplaces;

class employees extends Model
{
    use HasFactory;

    /**
     * relationship with client
     */
    public function client()
    {
        return $this->belongsTo(clients::class, 'empl_main_client_id', 'id');
    }

    /**
     * relationship with client place
     */
    public function clientplace()
    {
        return $this->belongsTo(clientplaces::class, 'empl_main_client_place_id', 'id');
    }

    /**
     * Relationship with employee hourly wages
     */
    public function employeepays()
    {
        return $this->hasMany(employeepays::class);
    }

    /**
     * Relationship with employee hourly wages
     */
    public function employeesalarys()
    {
        return $this->hasMany(employeesalarys::class);
    }

    /**
     * Relationship with salary
     */
    public function salarys()
    {
        return $this->hasMany(salary::class);
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'empl_cd',
        'empl_name_last',
        'empl_name_middle',
        'empl_name_first',
        'empl_kana_last',
        'empl_kana_middle',
        'empl_kana_first',
        'empl_alpha_last',
        'empl_alpha_middle',
        'empl_alpha_first',
        'empl_sex',
        'empl_email',
        'empl_mobile',
        'empl_hire_date',
        'empl_resign_date',
        'empl_paid_leave_pay',
        'empl_main_client_id',
        'empl_main_clientplace_id',
        'empl_main_client_name',
        'empl_wt_cd_list',
        'empl_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'empl_hire_date' => 'datetime',
            'empl_resign_date' => 'datetime',
        ];
    }

    /**
     * Set the employee's hire date.
     *
     * @param  string  $value
     * @return void
     */
    public function emplHireDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['empl_hire_date'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Set the employee's resign date.
     *
     * @param  string  $value
     * @return void
     */
    public function emplResignDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['empl_resign_date'] = $value === '' ? null : $value, 
        );
    }

    // 金額のアクセサとミューテータ
    public function emplPaidLeavePay(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['empl_paid_leave_pay'] = $value === '' ? null : $value, 
        );
    }
}
