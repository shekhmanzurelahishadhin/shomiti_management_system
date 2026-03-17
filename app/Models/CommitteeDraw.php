<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeDraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_id', 'member_id', 'draw_order',
        'draw_date', 'payout_amount', 'status', 'notes',
    ];

    protected $casts = [
        'draw_date' => 'date',
        'payout_amount' => 'decimal:2',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
