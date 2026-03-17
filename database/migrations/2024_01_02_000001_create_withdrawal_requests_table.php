<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            // Financials
            $table->decimal('share_amount',    10, 2)->default(0); // share capital
            $table->decimal('savings_amount',  10, 2)->default(0); // accumulated savings
            $table->decimal('profit_amount',   10, 2)->default(0); // profit share
            $table->decimal('total_amount',    10, 2)->default(0); // total repayable
            $table->decimal('repaid_amount',   10, 2)->default(0); // amount paid back so far

            // Reason & Timeline
            $table->text('reason')->nullable();
            $table->date('requested_date');
            $table->date('scheduled_repay_date')->nullable(); // when org plans to pay back

            // Status flow: pending → on_hold → partially_repaid → repaid → disconnected
            $table->enum('status', [
                'pending',           // request submitted, awaiting approval
                'on_hold',           // approved, membership on hold, awaiting repayment
                'partially_repaid',  // some money returned
                'repaid',            // fully repaid (membership becomes inactive/disconnected)
                'rejected',          // request denied
            ])->default('pending');

            $table->text('admin_note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('withdrawal_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount',   10, 2);
            $table->date('repay_date');
            $table->enum('method', ['cash','bank','bkash','nagad','other'])->default('cash');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_repayments');
        Schema::dropIfExists('withdrawal_requests');
    }
};
