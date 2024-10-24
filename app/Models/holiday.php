<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class holiday extends Model
{
    use HasFactory;

    /**
     * relationship with clients
     */
    public function client()
    {
        return $this->belongsTo(clients::class);
    }

    /**
     * attributes that are mass assignable
     */
    protected $fillable = [
        'holiday_date',
        'client_id',
        'holiday_name',
        'notes',
    ];

    /**
     * attributes that must be cast to native types
     */
    protected $casts = [
        'holiday_date' => 'datetime',
        'client_id' => 'integer',
        'holiday_name' => 'string',
        'notes' => 'string',
    ];

    /**
     * accessors and mutators
     */
    public function holidayDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0000-00-00 00:00:00' || $value === null) ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['holiday_date'] = $value === '' ? null : $value, 
        );
    }

    /**
     * 休日種別判定
     * @param string $date
     * @return int 3: 祝日 4: 顧客休日 0: 休日でない
     */
    static public function typeOfHoliday(string $date): int
    {
        $holiday = self::where('holiday_date', $date)->first();
        if($holiday)
        {
            return $holiday->client_id ? 4 : 3;
        }
        return 0;
    }
}
