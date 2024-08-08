<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class closepayrolls extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_year',
        'work_month',
        'closed',
        'opration_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opration_date' => 'datetime',
        ];
    }

    /**
     * Set the operation date.
     *
     * @param  string  $value
     * @return void
     */
    public function operationDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === '0000-00-00 00:00:00' || $value === null) ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['operation_date'] = $value === '' ? null : $value, 
        );
    }
}
