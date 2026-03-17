<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_request_id', 'member_id', 'amount',
        'repay_date', 'method', 'reference', 'note', 'paid_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'repay_date' => 'date',
    ];

    public function withdrawalRequest()
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
