<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    protected $signature   = 'bills:generate-monthly {--month= : Month (1-12)} {--year= : Year}';
    protected $description = 'Auto-generate monthly bills for all active members';

    public function handle(): void
    {
        $month   = $this->option('month') ?? now()->month;
        $year    = $this->option('year')  ?? now()->year;
        $dueDay  = (int) Setting::get('due_date_end', 15);
        $dueDate = Carbon::create($year, $month, $dueDay);

        $members = Member::whereIn('status', ['active', 'suspended'])->get();
        $created = 0;
        $skipped = 0;

        foreach ($members as $member) {
            $exists = Bill::where('member_id', $member->id)
                          ->where('bill_month', $month)
                          ->where('bill_year', $year)
                          ->exists();
            if ($exists) { $skipped++; continue; }

            Bill::create([
                'member_id'  => $member->id,
                'bill_month' => $month,
                'bill_year'  => $year,
                'amount'     => $member->monthly_deposit,
                'due_date'   => $dueDate,
            ]);
            $created++;
        }

        ActivityLog::create([
            'user_id'     => null,
            'action'      => 'generate',
            'model_type'  => 'Bill',
            'description' => "Scheduler: Generated {$created} bills for {$month}/{$year}, skipped {$skipped}",
            'ip_address'  => '127.0.0.1',
        ]);

        $this->info("Generated {$created} bills for {$month}/{$year}. Skipped: {$skipped}.");
    }
}
