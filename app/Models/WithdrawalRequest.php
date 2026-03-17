<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'share_amount', 'savings_amount', 'profit_amount',
        'total_amount', 'repaid_amount', 'reason', 'requested_date',
        'scheduled_repay_date', 'status', 'admin_note', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'requested_date'       => 'date',
        'scheduled_repay_date' => 'date',
        'approved_at'          => 'datetime',
        'share_amount'         => 'decimal:2',
        'savings_amount'       => 'decimal:2',
        'profit_amount'        => 'decimal:2',
        'total_amount'         => 'decimal:2',
        'repaid_amount'        => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function repayments()
    {
        return $this->hasMany(WithdrawalRepayment::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->repaid_amount);
    }

    public function getRepaymentPercentAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return round(($this->repaid_amount / $this->total_amount) * 100, 1);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'          => 'আবেদন বিবেচনাধীন',
            'on_hold'          => 'সদস্যপদ স্থগিত',
            'partially_repaid' => 'আংশিক পরিশোধ',
            'repaid'           => 'সম্পূর্ণ পরিশোধ',
            'rejected'         => 'প্রত্যাখ্যাত',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending'          => 'warning',
            'on_hold'          => 'info',
            'partially_repaid' => 'primary',
            'repaid'           => 'success',
            'rejected'         => 'danger',
        ][$this->status] ?? 'secondary';
    }
}
