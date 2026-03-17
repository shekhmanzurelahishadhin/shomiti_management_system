<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'name', 'father_name', 'mother_name', 'spouse_name',
        'date_of_birth', 'gender', 'marital_status', 'nid_or_birth_cert', 'photo',
        'present_village', 'present_post_office', 'present_union',
        'present_ward', 'present_upazila', 'present_district',
        'permanent_village', 'permanent_post_office', 'permanent_union',
        'permanent_ward', 'permanent_upazila', 'permanent_district',
        'phone',
        'nominee_name', 'nominee_father_spouse', 'nominee_relation',
        'nominee_phone', 'nominee_nid_or_birth_cert',
        'join_date', 'entry_fee', 'share_count', 'monthly_deposit',
        'referred_by_member_id', 'status', // active|inactive|suspended|on_hold|disconnected
    ];

    protected $casts = [
        'join_date'       => 'date',
        'date_of_birth'   => 'date',
        'monthly_deposit' => 'decimal:2',
        'entry_fee'       => 'decimal:2',
        'share_count'     => 'integer',
    ];

    public function bills()        { return $this->hasMany(Bill::class); }
    public function payments()     { return $this->hasMany(Payment::class); }
    public function committeeMembers() { return $this->hasMany(CommitteeMember::class); }
    public function referredBy()   { return $this->belongsTo(Member::class, 'referred_by_member_id'); }
    public function committees()   {
        return $this->belongsToMany(Committee::class, 'committee_members')
            ->withPivot('contribution_type', 'joined_at')->withTimestamps();
    }

    public static function generateMemberId(): string
    {
        $last    = self::latest('id')->first();
        $nextNum = $last ? ((int) substr($last->member_id, 3)) + 1 : 1;
        return 'NBD' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function getFullPresentAddressAttribute(): string
    {
        return collect([$this->present_village, $this->present_post_office,
            $this->present_union, $this->present_upazila, $this->present_district])
            ->filter()->implode(', ');
    }

    public function getFullPermanentAddressAttribute(): string
    {
        return collect([$this->permanent_village, $this->permanent_post_office,
            $this->permanent_union, $this->permanent_upazila, $this->permanent_district])
            ->filter()->implode(', ');
    }

    public function getPendingDuesAttribute(): float
    {
        return $this->bills()->whereIn('status', ['pending', 'partial', 'overdue'])
            ->selectRaw('SUM(amount + fine - discount - paid_amount) as total')
            ->value('total') ?? 0;
    }

    public function getShareValueAttribute(): float { return $this->share_count * 1000; }

    public function withdrawalRequests()
    {
        return $this->hasMany(\App\Models\WithdrawalRequest::class);
    }

    public function activeWithdrawal()
    {
        return $this->withdrawalRequests()
                    ->whereIn('status',['pending','on_hold','partially_repaid'])
                    ->latest()->first();
    }

    public function isDisconnected(): bool { return $this->status === 'disconnected'; }
    public function isOnHold():       bool { return $this->status === 'on_hold'; }

    public function getStatusLabelAttribute(): string
    {
        return [
            'active'       => 'সক্রিয়',
            'inactive'     => 'নিষ্ক্রিয়',
            'suspended'    => 'স্থগিত',
            'on_hold'      => 'অপেক্ষমান (উত্তোলন)',
            'disconnected' => 'সংযোগ বিচ্ছিন্ন',
        ][$this->status] ?? $this->status;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return [
            'active'       => 'success',
            'inactive'     => 'secondary',
            'suspended'    => 'danger',
            'on_hold'      => 'warning',
            'disconnected' => 'dark',
        ][$this->status] ?? 'secondary';
    }

}
