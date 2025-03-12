<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employeeallowdeduct extends Model
{
    use HasFactory;

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(employees::class);
    }
    public function masterallowdeduct()
    {
        return $this->belongsTo(masterallowdeducts::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'work_year',
        'work_month',
        'mad_cd',
        'mad_deduct',
        'amount',
        'notes',
    ];

    /**
     * 交通費を除く手当の合計を取得
     * @param int $employee_id
     * @param int $work_year
     * @param int $work_month
     * @return int
     */
    public static function getAllowTotal(int $employee_id, int $work_year, int $work_month): int
    {
        return self::where('employee_id', $employee_id)
            ->where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('mad_deduct', false)
            ->where('mad_cd', '!=', \App\Consts\AppConsts::MAD_CD_TRANSPORT)
            ->sum('amount');
    }

    /**
     * 控除の合計を取得
     * @param int $employee_id
     * @param int $work_year
     * @param int $work_month
     * @return int
     */
    public static function getDeductTotal(int $employee_id, int $work_year, int $work_month): int
    {
        return self::where('employee_id', $employee_id)
            ->where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('mad_deduct', true)
            ->sum('amount');
    }

    /**
     * 従業員の手当額を取得
     * @param int $employee_id
     * @param int $work_year
     * @param int $work_month
     * @param string $mad_cd
     * @return int
     */
    public static function getAllowAmount(int $employee_id, int $work_year, int $work_month, string $mad_cd): int
    {
        $Mad = self::where('employee_id', $employee_id)
            ->where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('mad_cd', $mad_cd)
            ->where('mad_deduct', 0)
            ->first();
        return $Mad ? $Mad->amount : 0;
    }

    /**
     * 従業員の控除額を取得
     * @param int $employee_id
     * @param int $work_year
     * @param int $work_month
     * @param string $mad_cd
     * @return int
     */
    public static function getDeductAmount(int $employee_id, int $work_year, int $work_month, string $mad_cd): int
    {
        $Mad = self::where('employee_id', $employee_id)
            ->where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('mad_cd', $mad_cd)
            ->where('mad_deduct', 1)
            ->first();
        return $Mad ? $Mad->amount : 0;
    }
}
