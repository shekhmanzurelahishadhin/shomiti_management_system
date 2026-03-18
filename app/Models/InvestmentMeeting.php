<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','meeting_date','venue','notes','status','created_by',
    ];

    protected $casts = ['meeting_date' => 'date'];

    public function items()
    {
        return $this->hasMany(InvestmentMeetingItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return ['scheduled'=>'নির্ধারিত','held'=>'অনুষ্ঠিত','cancelled'=>'বাতিল'][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return ['scheduled'=>'info','held'=>'success','cancelled'=>'danger'][$this->status] ?? 'secondary';
    }
}
