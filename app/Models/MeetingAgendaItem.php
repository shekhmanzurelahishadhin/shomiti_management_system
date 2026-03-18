<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id','investment_request_id','agenda_order','discussion_notes',
        'decision','approved_amount','profit_ratio','investment_start_date',
        'maturity_date','decision_notes','decided_by','decided_at',
    ];

    protected $casts = [
        'approved_amount'      => 'decimal:2',
        'investment_start_date'=> 'date',
        'maturity_date'        => 'date',
        'decided_at'           => 'datetime',
    ];

    public function meeting()           { return $this->belongsTo(Meeting::class); }
    public function investmentRequest() { return $this->belongsTo(InvestmentRequest::class); }
    public function decidedBy()         { return $this->belongsTo(User::class, 'decided_by'); }
}
