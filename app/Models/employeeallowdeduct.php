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
}
