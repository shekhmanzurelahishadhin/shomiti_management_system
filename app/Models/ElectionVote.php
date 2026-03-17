<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id','election_position_id','election_candidate_id','voter_member_id',
    ];

    protected $casts = ['voted_at' => 'datetime'];

    public function election()   { return $this->belongsTo(Election::class); }
    public function position()   { return $this->belongsTo(ElectionPosition::class,'election_position_id'); }
    public function candidate()  { return $this->belongsTo(ElectionCandidate::class,'election_candidate_id'); }
    public function voter()      { return $this->belongsTo(Member::class,'voter_member_id'); }
}
