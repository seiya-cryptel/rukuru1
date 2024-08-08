<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pricetables extends Model
{
    use HasFactory;

    /**
     * Relationship with client
     */
    public function client()
    {
        return $this->belongsTo(clients::class);
    }
    /**
     * Relationship with clientplace
     */
    public function clientplace()
    {
        return $this->belongsTo(clientplaces::class);
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
        'bill_name',
        'bill_print_name',
        'bill_unitprice',
        'display_order',
        'notes',
    ];
}
