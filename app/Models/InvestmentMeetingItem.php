<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentMeetingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_meeting_id','investment_request_id','agenda_order','decision','decision_note',
    ];

    public function meeting()           { return $this->belongsTo(InvestmentMeeting::class,'investment_meeting_id'); }
    public function investmentRequest() { return $this->belongsTo(InvestmentRequest::class,'investment_request_id'); }

    public function getDecisionLabelAttribute(): string
    {
        return ['pending'=>'বিবেচনাধীন','approved'=>'অনুমোদিত','rejected'=>'প্রত্যাখ্যাত','modification_needed'=>'সংশোধন'][$this->decision] ?? $this->decision;
    }
}
