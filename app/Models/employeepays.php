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
