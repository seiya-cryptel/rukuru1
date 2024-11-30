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
     * 顧客、事業所、従業員の指定年月データを削除する
     * @param int $client_id
     * @param int $clientplace_id
     * @param int $employee_id
     * @param int $targetYear
     * @param int $targetMonth
     */
    public static function deleteWorkData(int $client_id, int $clientplace_id, int $employee_id, int $targetYear, int $targetMonth)
    {
        employeeworks::where('client_id', $client_id)
            ->where('clientplace_id', $clientplace_id)
            ->where('employee_id', $employee_id)
            ->whereYear('wrk_date', $targetYear)
            ->whereMonth('wrk_date', $targetMonth)
            ->delete();
    }

    /**
     * 顧客、事業所、従業員の指定年月以前の最後の勤怠データを取得
     * @param int $client_id
     * @param int $clientplace_id
     * @param int $employee_id
     * @param int $targetYear
     * @param int $targetMonth
     */
    public static function getPreviousWorkData(int $client_id, int $clientplace_id, int $employee_id, int $targetYear, int $targetMonth)    
    {
        $targetDate = $targetYear . '-' . $targetMonth . '-01';

        $workData = employeeworks::where('client_id', $client_id)
            ->where('clientplace_id', $clientplace_id)
            ->where('employee_id', $employee_id)
            ->whereYear('wrk_date', '<', $targetDate)
            ->orderBy('wrk_date', 'desc')
            ->orderBy('wrk_seq', 'desc')
            ->first();

        return $workData;
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
            get: fn ($value) => ($value === null) ? '' : date('H:i', strtotime($value)),
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
            get: fn ($value) => ($value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_log_end'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Accesor/Mutator for wrk_work_start
     *
     * @param  string  $value
     * @return void
     */
    public function wrkWorkStart(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === null) ? '' : date('Y-m-d H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_work_start'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Accesor/Mutator for wrk_work_end
     *
     * @param  string  $value
     * @return void
     */
    public function wrkWorkEnd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === null) ? '' : date('Y-m-d H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wrk_work_end'] = $value === '' ? null : $value, 
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
