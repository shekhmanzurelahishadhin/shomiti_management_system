<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'bill_month', 'bill_year', 'amount',
        'fine', 'discount', 'paid_amount', 'status',
        'due_date', 'fine_waived', 'fine_waive_reason',
        'notes', 'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'fine' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'fine_waived' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalDueAttribute(): float
    {
        return max(0, $this->amount + $this->fine - $this->discount - $this->paid_amount);
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->bill_month, 1));
    }

    public function updateStatus(): void
    {
        $total = $this->amount + $this->fine - $this->discount;
        if ($this->paid_amount <= 0) {
            $this->status = now()->gt($this->due_date) ? 'overdue' : 'pending';
        } elseif ($this->paid_amount >= $total) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }
        $this->save();
    }
}
