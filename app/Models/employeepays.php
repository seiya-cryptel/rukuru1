<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    public function clientworktype()
    {
        return $this->belongsTo(clientworktypes::class);
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
     * The attributes that should be cast.
     */
    public function wtPayStd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_std'] = $value === '' ? null : (int)str_replace(',', '', $value) 
        );
    }
    public function wtPayOvr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_ovr'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtPayOvrMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_ovr_midnight'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtPayHoliday(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_holiday'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtPayHolidayMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_holiday_midnight'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtBillStd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_std'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtBillOvr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_ovr'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtBillOvrMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_ovr_midnight'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtBillHoliday(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_holiday'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }
    public function wtBillHolidayMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_holiday_midnight'] = $value === '' ? null : (int)str_replace(',', '', $value) , 
        );
    }

    /**
     * 従業員時給を検索する
     * @param int $employeeId 従業員ID
     * @param int $clientId 顧客ID
     * @param int $clientPlaceId 部門ID
     * @param string $wtCd 作業種別コード
     * @return integer[] [標準時給, 残業時給, 深夜残業時給, 法定休日時給, 法定休日深夜残業時給] | null
     * 顧客、部門、作業種別が一致する時給があればそれを返す
     * 顧客、作業種別が一致する時給があればそれを返す
     * 作業種別が一致する時給があればそれを返す
     */
    static public function getPayhour($employeeId, $clientId, $clientPlaceId, $wtCd)
    {
        // 顧客IDと部門IDが一致する時給を取得
        $EmployeePay = employeepays::with('clientworktypes')
            ->join('clientworktypes', 'clientworktypes.id', '=', 'employeepays.clientworktype_id')
            ->where('employee_id', $employeeId)
            ->where('clientworktypes.client_id', $clientId)
            ->where('clientworktypes.clientplace_id', $clientPlaceId)
            ->where('clientworktypes.wt_cd', $wtCd)
            ->first();
        if ($EmployeePay) {
            return $EmployeePay->payhour;
        }
    
        return null;
    }
}
