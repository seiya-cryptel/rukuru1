<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\clientworktypes as modelClientworktypes;

class clientworktypes extends Model
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
        'wt_name',
        'wt_kana',
        'wt_alpha',
        'wt_notes',
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

    /**
     * possible work types by client and client place
     *
     * @return array<string, string>
     */
    static public function possibleWorkTypes($client_id, $clientplace_id)
    {
        $workTypesArray = [];

        // specific work types for the client and client place
        $workTypes = modelClientworktypes::where('client_id', $client_id)
            ->where('clientplace_id', $clientplace_id)
            ->get();
        foreach ($workTypes as $workType) {
            $workTypesArray[$workType->wt_cd] = $workType->wt_name;
        }

        // specific work types for the client
        $workTypes = modelClientworktypes::where('client_id', $client_id)
            ->whereNull('clientplace_id')
            ->get();
        foreach ($workTypes as $workType) {
            if(! array_key_exists($workType->wt_cd, $workTypesArray)) {
                $workTypesArray[$workType->wt_cd] = $workType->wt_name;
            }
        }

        // specific work types for general
        $workTypes = modelClientworktypes::whereNull('client_id')
            ->whereNull('clientplace_id')
            ->get();
        foreach ($workTypes as $workType) {
            if(! array_key_exists($workType->wt_cd, $workTypesArray)) {
                $workTypesArray[$workType->wt_cd] = $workType->wt_name;
            }
        }
    
        return $workTypesArray;
    }
}
