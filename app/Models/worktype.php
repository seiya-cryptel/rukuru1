<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * 勤務体系マスタ
 */
class worktype extends Model
{
    use HasFactory;

    /**
     * Attributes
     */
    protected $fillable = [
        'worktype_kintai',
        'worktype_cd',
        'worktype_name',
        'worktype_time_spec',
        'worktype_time_start',
        'worktype_time_end',
        'notes',
    ];

    /**
     * casts
     */
    protected function casts(): array
    {
        return [
            'worktype_time_start' => 'datetime',
            'worktype_time_end' => 'datetime',
        ];
    }

    /**
     * accessors and mutators
     */
    public function worktypeTimeStart(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['worktype_time_start'] = $value === '' ? null : $value, 
        );
    }
    public function worktypeTimeEnd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['worktype_time_end'] = $value === '' ? null : $value, 
        );
    }

}
