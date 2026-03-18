<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'status', 'phone', 'member_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** The linked member record (if this user is also a member) */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /** Is this user a regular member (not admin/treasurer)? */
    public function isMemberRole(): bool
    {
        return $this->hasRole('Member') && !$this->hasAnyRole(['Super Admin','Admin','Treasurer']);
    }

    /** Get the linked Member model for the logged-in member user */
    public function getLinkedMember(): ?Member
    {
        return $this->member_id ? $this->member : null;
    }
}
