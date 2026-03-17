<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','election_year',
        'nomination_start','nomination_end',
        'voting_start','voting_end',
        'status','created_by',
    ];

    protected $casts = [
        'nomination_start' => 'date',
        'nomination_end'   => 'date',
        'voting_start'     => 'date',
        'voting_end'       => 'date',
    ];

    public function positions()
    {
        return $this->hasMany(ElectionPosition::class);
    }

    public function candidates()
    {
        return $this->hasMany(ElectionCandidate::class);
    }

    public function votes()
    {
        return $this->hasMany(ElectionVote::class);
    }

    public function results()
    {
        return $this->hasMany(ElectionResult::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isVotingOpen(): bool
    {
        return $this->status === 'voting'
            && now()->between($this->voting_start, $this->voting_end);
    }

    public function isNominationOpen(): bool
    {
        return $this->status === 'nomination'
            && now()->between($this->nomination_start, $this->nomination_end);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'upcoming'    => 'আসন্ন',
            'nomination'  => 'মনোনয়ন চলছে',
            'voting'      => 'ভোটগ্রহণ চলছে',
            'counting'    => 'গণনা চলছে',
            'completed'   => 'সম্পন্ন',
            'cancelled'   => 'বাতিল',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'upcoming'   => 'secondary',
            'nomination' => 'info',
            'voting'     => 'success',
            'counting'   => 'warning',
            'completed'  => 'primary',
            'cancelled'  => 'danger',
        ][$this->status] ?? 'secondary';
    }
}
