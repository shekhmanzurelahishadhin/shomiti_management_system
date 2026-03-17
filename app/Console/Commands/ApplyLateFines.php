<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Console\Command;

class ApplyLateFines extends Command
{
    protected $signature   = 'bills:apply-fines';
    protected $description = 'Apply late fees to overdue bills and auto-suspend members per constitution rules';

    public function handle(): void
    {
        $lateFee      = (float) Setting::get('late_fee', 50);
        $suspendAfter = (int)   Setting::get('suspend_after_months', 3);
        $updated      = 0;
        $suspended    = 0;

        // Apply fines to overdue unpaid bills
        $overdueBills = Bill::whereIn('status', ['pending', 'partial'])
                            ->where('due_date', '<', now())
                            ->where('fine', 0)
                            ->where('fine_waived', false)
                            ->get();

        foreach ($overdueBills as $bill) {
            $bill->update(['fine' => $lateFee, 'status' => 'overdue']);
            $updated++;
        }

        // Auto-suspend: members with 3+ consecutive unpaid months (গঠনতন্ত্র ধারা ৭.১)
        $activeMembers = Member::where('status', 'active')->get();
        foreach ($activeMembers as $member) {
            $unpaidCount = Bill::where('member_id', $member->id)
                               ->whereIn('status', ['pending', 'overdue'])
                               ->where('bill_year',  now()->year)
                               ->count();

            if ($unpaidCount >= $suspendAfter) {
                $member->update(['status' => 'suspended']);
                ActivityLog::create([
                    'user_id'     => null,
                    'action'      => 'suspend',
                    'model_type'  => 'Member',
                    'model_id'    => $member->id,
                    'description' => "Auto-suspended: {$member->name} — টানা {$unpaidCount} মাস চাঁদা না দেওয়ার কারণে",
                    'ip_address'  => '127.0.0.1',
                ]);
                $suspended++;
            }
        }

        ActivityLog::create([
            'user_id'     => null,
            'action'      => 'fine',
            'model_type'  => 'Bill',
            'description' => "Scheduler: ৳{$lateFee} জরিমানা {$updated}টি বিলে প্রযোগ। {$suspended} জন সদস্য স্থগিত।",
            'ip_address'  => '127.0.0.1',
        ]);

        $this->info("Applied fines to {$updated} bills. Suspended {$suspended} members.");
    }
}
