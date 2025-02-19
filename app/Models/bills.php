<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\billdetails as modelBillDetails;

class bills extends Model
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
     * Relationship with client place
     */
    public function clientplace()
    {
        return $this->belongsTo(clientplaces::class);
    }
    /**
     * Relationship with bill details
     */
    public function billdetail()
    {
        return $this->hasMany(billdetails::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bill_no',
        'bill_date',
        'client_id',
        'clientplace_id',
        'work_year',
        'work_month',
        'bill_title',
        'bill_amount',
        'bill_tax',
        'bill_total',
        'notes',
    ];

    /**
     * 顧客ID、部門IDと作業年月から請求書情報を作成または再作成
     */
    public static function createBill($clientId, $clientPlaceId, $workYear, $workMonth)
    {
        // 顧客ID、部門ID、作業年月に該当する請求書情報を取得
        $bill = bills::where('client_id', $clientId)
            ->where('clientplace_id', $clientPlaceId)
            ->where('work_year', $workYear)
            ->where('work_month', $workMonth)
            ->first();
        if($bill)
        {
            // 該当する請求書情報が存在する場合は、請求書明細情報を削除
            modelBillDetails::where('bill_id', $bill->id)->delete();
        }
        else
        {
            // 該当する請求書情報が存在しない場合は、新規作成
            $bill = new bills();
        }

        // 請求書情報の初期値を設定
        $bill->client_id = $clientId;
        $bill->clientplace_id = $clientPlaceId;
        $bill->work_year = $workYear;
        $bill->work_month = $workMonth;
        $bill->bill_no = '';
        $bill->bill_date = date('Y-m-d');
        $bill->bill_title = '請求書';
        $bill->bill_amount = 0;
        $bill->bill_tax = 0;
        $bill->bill_total = 0;
        $bill->notes = '';
        $bill->save();

        return $bill;
    }
}
