<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class employeeworks extends Model
{
    use HasFactory;

    /**
     * Relationship with employee
     */
    public function employee()
    {
        return $this->belongsTo(employees::class);
    }

    /**
     * Relationship with client
     */
    public function client()
    {
        return $this->belongsTo(clients::class);
    }

    /**
     * Relationship with client place
     */
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
        'wrk_seq',
        'leave',
        'client_id',
        'clientplace_id',
        'wt_cd',
        'wrk_log_start',
        'wrk_log_end',
        'wrk_work_start',
        'wrk_work_end',
        'wrk_work_hours',
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
            'wrk_log_start' => 'datetime',
            'wrk_log_end' => 'datetime',
            'wrk_work_start' => 'datetime',
            'wrk_work_end' => 'datetime',
            'wrk_work_hours' => 'datetime',
        ];
    }

    /**
     * Accesor/Mutator for wrk_log_start
     *
     * @param  string  $value
     * @return void
     */
    public function wrkLogStart(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_log_start'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Accesor/Mutator for wrk_log_end
     *
     * @param  string  $value
     * @return void
     */
    public function wrkLogEnd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_log_end'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Accesor/Mutator for wrk_work_hours
     *
     * @param  string  $value
     * @return void
     */
    public function wrkWorkHours(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('h:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_work_hours'] = $value === '' ? null : $value, 
        );
    }
}
