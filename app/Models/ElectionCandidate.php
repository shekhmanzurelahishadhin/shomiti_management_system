<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id','election_position_id','member_id','manifesto','status',
    ];

    public function election()  { return $this->belongsTo(Election::class); }
    public function position()  { return $this->belongsTo(ElectionPosition::class,'election_position_id'); }
    public function member()    { return $this->belongsTo(Member::class); }
    public function votes()     { return $this->hasMany(ElectionVote::class); }
    public function result()    { return $this->hasOne(ElectionResult::class); }

    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
