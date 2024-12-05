<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class employeesalarys extends Model
{
    use HasFactory;

    protected $table = 'employeesalary';

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(employees::class);
    }
    public function client()
    {
        return $this->belongsTo(clients::class);
    }
    public function clientplace()
    {
        return $this->belongsTo(clientplaces::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'wrk_date',
        'wrk_ttl_seq',
        'leave',
        'client_id',
        'clientplace_id',
        'wt_cd',
        'wrk_work_start',
        'wrk_work_end',
        'wrk_work_hours',
        'payhour',
        'premium',
        'wrk_pay',
        'wt_bill_item_cd',
        'wt_bill_item_name',
        'billhour',
        'billpremium',
        'wrk_bill',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wrk_work_start' => 'datetime',
            'wrk_work_end' => 'datetime',
            'wrk_work_hours' => 'datetime',
        ];
    }

    /**
     * Accesor/Mutator
     *
     * @param  string  $value
     * @return void
     */
    public function wrkDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_date'] = $value === '' ? null : $value, 
        );
    }

    public function wrkWorkStart(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_work_start'] = $value === '' ? null : $value, 
        );
    }

    public function wrkWorkEnd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_work_end'] = $value === '' ? null : $value, 
        );
    }

    public function wrkWorkHours(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('h:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_work_hours'] = $value === '' ? null : $value, 
        );
    }
}
