<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billdetails extends Model
{
    use HasFactory;
    /**
     * Relationship with client
     */
    public function bill()
    {
        return $this->belongsTo(bills::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bill_id',
        'display_order',
        'title',
        'unit_price',
        'quantity',
        'unit',
        'amount',
        'tax',
        'total',
        'notes',
    ];
}
