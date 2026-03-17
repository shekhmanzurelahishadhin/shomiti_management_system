<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'total_fund', 'status',
    ];

    protected $casts = [
        'total_fund' => 'decimal:2',
    ];

    public function committeeMembers()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'committee_members')
            ->withPivot('contribution_type', 'joined_at')
            ->withTimestamps();
    }

    public function draws()
    {
        return $this->hasMany(CommitteeDraw::class);
    }
}
