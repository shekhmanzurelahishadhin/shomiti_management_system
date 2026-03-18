<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InvestmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id','project_name','project_description',
        'requested_amount','duration_months','expected_profit_ratio','expected_return_date',
        'submitted_date',
        'approved_amount','approved_duration_months','approved_profit_ratio',
        'approved_start_date','approved_return_date',
        'approval_note','rejection_note','modification_note',
        'status','approved_by','approved_at','created_by',
    ];

    protected $casts = [
        'expected_return_date'   => 'date',
        'submitted_date'         => 'date',
        'approved_start_date'    => 'date',
        'approved_return_date'   => 'date',
        'approved_at'            => 'datetime',
        'requested_amount'       => 'decimal:2',
        'approved_amount'        => 'decimal:2',
        'expected_profit_ratio'  => 'decimal:2',
        'approved_profit_ratio'  => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────────────────────────────
    public function member()      { return $this->belongsTo(Member::class); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }
    public function payment()     { return $this->hasOne(InvestmentPayment::class); }
    public function settlement()  { return $this->hasOne(InvestmentSettlement::class); }
    public function meetingItems(){ return $this->hasMany(InvestmentMeetingItem::class); }

    // ── Computed Attributes ────────────────────────────────────────────
    public function getExpectedProfitAmountAttribute(): float
    {
        $amt   = (float)($this->approved_amount ?? $this->requested_amount);
        $ratio = (float)($this->approved_profit_ratio ?? $this->expected_profit_ratio);
        return round($amt * $ratio / 100, 2);
    }

    public function getExpectedReturnAmountAttribute(): float
    {
        return (float)($this->approved_amount ?? $this->requested_amount) + $this->expected_profit_amount;
    }

    public function getMaturityDateAttribute(): ?Carbon
    {
        if (!$this->approved_start_date) return null;
        return $this->approved_start_date->addMonths($this->approved_duration_months ?? $this->duration_months);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->maturity_date) return null;
        return max(0, now()->diffInDays($this->maturity_date, false));
    }

    public function getIsMaturedAttribute(): bool
    {
        return $this->maturity_date && now()->gte($this->maturity_date);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'             => 'বিবেচনাধীন',
            'in_agenda'           => 'সভায় অন্তর্ভুক্ত',
            'approved'            => 'অনুমোদিত',
            'rejected'            => 'প্রত্যাখ্যাত',
            'modification_needed' => 'সংশোধন প্রয়োজন',
            'active'              => 'সক্রিয়',
            'matured'             => 'মেয়াদ শেষ',
            'closed'              => 'নিষ্পন্ন',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending'             => 'warning',
            'in_agenda'           => 'info',
            'approved'            => 'primary',
            'rejected'            => 'danger',
            'modification_needed' => 'secondary',
            'active'              => 'success',
            'matured'             => 'dark',
            'closed'              => 'light',
        ][$this->status] ?? 'secondary';
    }

    // ── Scopes ─────────────────────────────────────────────────────────
    public function scopeActive($q)   { return $q->where('status','active'); }
    public function scopeMatured($q)  { return $q->where('status','matured'); }
    public function scopePending($q)  { return $q->where('status','pending'); }
    public function scopeApproved($q) { return $q->where('status','approved'); }
}
