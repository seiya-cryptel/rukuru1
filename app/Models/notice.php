<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class notice extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notice_date' => 'datetime',
        ];
    }

    /**
     * Set the notice date.
     *
     * @param  string  $value
     * @return void
     */
    public function noticeDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['notice_date'] = $value === '' ? null : $value, 
        );
    }
}
