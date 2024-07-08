<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appsettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sys_name',
        'sys_index',
        'sys_istext',
        'sys_txtval',
        'sys_numval',
        'sys_notes',
    ];
}
