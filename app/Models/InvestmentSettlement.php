<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_request_id','member_id','voucher_number',
        'investment_amount','actual_profit_loss','outcome','return_amount',
        'payment_method','reference','bank_name','settlement_date','note','settled_by',
    ];

    protected $casts = [
        'investment_amount'   => 'decimal:2',
        'actual_profit_loss'  => 'decimal:2',
        'return_amount'       => 'decimal:2',
        'settlement_date'     => 'date',
    ];

    public function investmentRequest() { return $this->belongsTo(InvestmentRequest::class,'investment_request_id'); }
    public function member()            { return $this->belongsTo(Member::class); }
    public function settledBy()         { return $this->belongsTo(User::class,'settled_by'); }

    public static function generateVoucher(): string
    {
        $last = self::latest('id')->first();
        $num  = $last ? ((int)substr($last->voucher_number, 4)) + 1 : 1;
        return 'SET-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    public function getOutcomeLabelAttribute(): string
    {
        return ['profit'=>'লাভ','loss'=>'ক্ষতি','breakeven'=>'সমতা'][$this->outcome] ?? $this->outcome;
    }
}
