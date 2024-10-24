<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

use App\Models\clientworktypes as modelClientworktypes;

class clientworktypes extends Model
{
    use HasFactory;

    /**
     * Relationship
     */
    public function client()
    {
        return $this->belongsTo(clients::class);
    }
    public function clientplace()
    {
        return $this->belongsTo(clientplaces::class);
    }
    public function employeepays()
    {
        return $this->hasMany(employeepays::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'clientplace_id',
        'wt_cd',
        'wt_name',
        'wt_kana',
        'wt_alpha',
        'wt_day_night',
        'wt_work_start',
        'wt_work_end',
        'wt_lunch_break',
        'wt_evening_break',
        'wt_night_break',
        'wt_midnight_break',
        'wt_pay_std',
        'wt_pay_ovr',
        'wt_pay_ovr_midnight',
        'wt_pay_holiday',
        'wt_pay_holiday_midnight',
        'wt_bill_std',
        'wt_bill_ovr',
        'wt_bill_ovr_midnight',
        'wt_bill_holiday',
        'wt_bill_holiday_midnight',
        'wt_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wt_work_start' => 'datetime',
            'wt_work_end' => 'datetime',
            'wt_lunch_break' => 'datetime',
            'wt_evening_break' => 'datetime',
            'wt_night_break' => 'datetime',
            'wt_midnight_break' => 'datetime',
        ];
    }

    /**
     * accessors and mutators
     */
    // 時刻や時間のアクセサとミューテータ
    public function wtWorkStart(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_work_start'] = $value === '' ? null : $value, 
        );
    }
    public function wtWorkEnd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_work_end'] = $value === '' ? null : $value, 
        );
    }
    public function wtLunchBreak(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_lunch_break'] = $value === '' ? null : $value, 
        );
    }
    public function wtEveningBreak(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_evening_break'] = $value === '' ? null : $value, 
        );
    }
    public function wtNightBreak(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_night_break'] = $value === '' ? null : $value, 
        );
    }
    public function wtMidnightBreak(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '00:00:00' || $value === null) ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['wt_midnight_break'] = $value === '' ? null : $value, 
        );
    }
    // 金額のアクセサとミューテータ
    public function wtPayStd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_std'] = $value === '' ? null : $value, 
        );
    }
    public function wtPayOvr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_ovr'] = $value === '' ? null : $value , 
        );
    }
    public function wtPayOvrMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_ovr_midnight'] = $value === '' ? null : $value , 
        );
    }
    public function wtPayHoliday(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_holiday'] = $value === '' ? null : $value , 
        );
    }
    public function wtPayHolidayMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_pay_holiday_midnight'] = $value === '' ? null : $value , 
        );
    }
    public function wtBillStd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_std'] = $value === '' ? null : $value , 
        );
    }
    public function wtBillOvr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_ovr'] = $value === '' ? null : $value , 
        );
    }
    public function wtBillOvrMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_ovr_midnight'] = $value === '' ? null : $value , 
        );
    }
    public function wtBillHoliday(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_holiday'] = $value === '' ? null : $value , 
        );
    }
    public function wtBillHolidayMidnight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0' || $value === null) ? '' : number_format($value),
            set: fn ($value) => $this->attributes['wt_bill_holiday_midnight'] = $value === '' ? null : $value , 
        );
    }

    /**
     * possible work types by client and client place
     *
     * @return array<string, string>
     */
    static public function possibleWorkTypes($client_id, $clientplace_id)
    {
        $workTypesArray = [];

        // specific work types for the client and client place
        $workTypes = modelClientworktypes::where('client_id', $client_id)
            ->where('clientplace_id', $clientplace_id)
            ->get();
        foreach ($workTypes as $workType) {
            $workTypesArray[$workType->wt_cd] = $workType->wt_name;
        }

        // specific work types for the client
        $workTypes = modelClientworktypes::where('client_id', $client_id)
            ->whereNull('clientplace_id')
            ->get();
        foreach ($workTypes as $workType) {
            if(! array_key_exists($workType->wt_cd, $workTypesArray)) {
                $workTypesArray[$workType->wt_cd] = $workType->wt_name;
            }
        }

        // specific work types for general
        $workTypes = modelClientworktypes::whereNull('client_id')
            ->whereNull('clientplace_id')
            ->get();
        foreach ($workTypes as $workType) {
            if(! array_key_exists($workType->wt_cd, $workTypesArray)) {
                $workTypesArray[$workType->wt_cd] = $workType->wt_name;
            }
        }
    
        return $workTypesArray;
    }

    /**
     * possible work types by client, client place and work type
     * @parametors: $client_id, $clientplace_id, $wt_cd
     * @return clientworktypes
     */
    static public function getSutable($client_id, $clientplace_id, $wt_cd) : ?clientworktypes
    {
        $workType = modelClientworktypes::where('client_id', $client_id)
            ->where('clientplace_id', $clientplace_id)
            ->where('wt_cd', $wt_cd)
            ->first();
        if($workType === null) {
            $workType = modelClientworktypes::where('client_id', $client_id)
                ->whereNull('clientplace_id')
                ->where('wt_cd', $wt_cd)
                ->first();
        }
        if($workType === null) {
            $workType = modelClientworktypes::whereNull('client_id')
                ->whereNull('clientplace_id')
                ->where('wt_cd', $wt_cd)
                ->first();
        }
        return $workType;
    }
}
