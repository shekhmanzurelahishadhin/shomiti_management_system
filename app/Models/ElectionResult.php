<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id','election_position_id','election_candidate_id',
        'member_id','vote_count','is_elected',
    ];

    protected $casts = ['is_elected' => 'boolean'];

    public function election()   { return $this->belongsTo(Election::class); }
    public function position()   { return $this->belongsTo(ElectionPosition::class,'election_position_id'); }
    public function candidate()  { return $this->belongsTo(ElectionCandidate::class,'election_candidate_id'); }
    public function member()     { return $this->belongsTo(Member::class); }
}
