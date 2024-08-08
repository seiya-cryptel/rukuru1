<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employeepays extends Model
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'client_id',
        'clientplace_id',
        'wt_cd',
        'payhour',
        'billhour',
        'notes',
    ];

    /**
     * 時給検索
     */
    static public function getPayhour($employeeId, $clientId, $clientPlaceId, $wtCd)
    {
        // 顧客IDと事業所IDが一致する時給を取得
        $EmployeePay = employeepays::where('employee_id', $employeeId)
            ->where('client_id', $clientId)
            ->where('clientplace_id', $clientPlaceId)
            ->where('wt_cd', $wtCd)
            ->first();
        if ($EmployeePay) {
            return $EmployeePay->payhour;
        }
        
        // 顧客IDが一致する時給を取得
        $EmployeePay = employeepays::where('employee_id', $employeeId)
            ->where('client_id', $clientId)
            ->whereNull('clientplace_id')
            ->where('wt_cd', $wtCd)
            ->first();
        if ($EmployeePay) {
            return $EmployeePay->payhour;
        }
        
        // 作業種別で時給を取得
        $EmployeePay = employeepays::where('employee_id', $employeeId)
            ->whereNull('client_id')
            ->whereNull('clientplace_id')
            ->where('wt_cd', $wtCd)
            ->first();
        if ($EmployeePay) {
            return $EmployeePay->payhour;
        }
    
        throw new \Exception('時給が設定されていません');
    }
}
