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
            get: fn ($value) => $value === null ? '' : date('Y-m-d', strtotime($value)),
            set: fn ($value) => $this->attributes['operation_date'] = $value === '' ? null : $value, 
        );
    }

    /**
     * 締め処理を行う
     * @param  int  $work_year
     * @param  int  $work_month
     * @param  int  $client_id
     * @return void
     * @throws \Exception
     * 
     * $client_id が0の場合は、給与の締め処理を表す
     */
    static public function closePayroll(int $work_year, int $work_month, int $client_id = 0): void
    {
        $Closepayroll = self::where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('client_id', $client_id)
            ->first();
        if(!$Closepayroll) {
            $Closepayroll = new self();
        }

        // 締め処理を行う
        $Closepayroll->work_year = $work_year;
        $Closepayroll->work_month = $work_month;
        $Closepayroll->client_id = $client_id;
        $Closepayroll->closed = 1;
        $Closepayroll->operation_date = date('Y-m-d');
        $Closepayroll->save();
    }

    /**
     * 締め処理を解除する
     * @param  int  $work_year
     * @param  int  $work_month
     * @param  int  $client_id
     * @return void
     * @throws \Exception
     */
    static public function openPayroll(int $work_year, int $work_month, int $client_id = 0): void
    {
        $Closepayroll = self::where('work_year', $work_year)
            ->where('work_month', $work_month)
            ->where('client_id', $client_id)
            ->first();
        if(!$Closepayroll) {
            $Closepayroll = new self();
        }

        // 締め処理を行う
        $Closepayroll->work_year = $work_year;
        $Closepayroll->work_month = $work_month;
        $Closepayroll->client_id = $client_id;
        $Closepayroll->closed = 0;
        $Closepayroll->operation_date = date('Y-m-d');
        $Closepayroll->save();
    }
}
