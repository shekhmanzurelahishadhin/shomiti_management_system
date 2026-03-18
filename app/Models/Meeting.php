<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','meeting_date','meeting_time','venue',
        'description','status','minutes','created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function investmentRequests()
    {
        return $this->hasMany(InvestmentRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return ['scheduled'=>'নির্ধারিত','ongoing'=>'চলছে','completed'=>'সম্পন্ন','cancelled'=>'বাতিল'][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return ['scheduled'=>'info','ongoing'=>'warning','completed'=>'success','cancelled'=>'danger'][$this->status] ?? 'secondary';
    }
}
