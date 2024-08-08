<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientplaces extends Model
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
     * Relationship with client places
     */
    public function pricetables()
    {
        return $this->hasMany(pricetables::class);
    }
    /**
     * Relationship with bills
     */
    public function bills()
    {
        return $this->hasMany(bills::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'cl_pl_cd',
        'cl_pl_name',
        'cl_pl_kana',
        'cl_pl_alpha',
        'cl_pl_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        ];
    }
}
