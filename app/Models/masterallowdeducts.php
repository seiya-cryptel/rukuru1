<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class masterallowdeducts extends Model
{
    use HasFactory;

    /**
     * relationships
     */
    public function employeeallowdeducts()
    {
        return $this->hasMany(employeeallowdeduct::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mad_cd',
        'mad_allow',
        'mad_deduct',
        'mad_name',
        'mad_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mad_allow' => 'boolean',
            'mad_deduct' => 'boolean',
        ];
    }
}
