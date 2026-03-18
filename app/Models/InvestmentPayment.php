<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_request_id','member_id','voucher_number','amount',
        'payment_method','reference','bank_name','account_number',
        'payment_date','note','paid_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function investmentRequest() { return $this->belongsTo(InvestmentRequest::class,'investment_request_id'); }
    public function member()            { return $this->belongsTo(Member::class); }
    public function paidBy()            { return $this->belongsTo(User::class,'paid_by'); }

    public static function generateVoucher(): string
    {
        $last = self::latest('id')->first();
        $num  = $last ? ((int)substr($last->voucher_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
