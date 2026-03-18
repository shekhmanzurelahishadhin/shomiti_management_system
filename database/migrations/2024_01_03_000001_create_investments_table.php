<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Investment Requests ─────────────────────────────────────────
        Schema::create('investment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();

            // Request fields
            $table->string('project_name');
            $table->text('project_description')->nullable();
            $table->decimal('requested_amount', 12, 2);
            $table->unsignedInteger('duration_months');       // in months
            $table->decimal('expected_profit_ratio', 5, 2)->default(0); // % per period
            $table->date('expected_return_date');
            $table->date('submitted_date');

            // Approval fields (filled when approved)
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->unsignedInteger('approved_duration_months')->nullable();
            $table->decimal('approved_profit_ratio', 5, 2)->nullable();
            $table->date('approved_start_date')->nullable();
            $table->date('approved_return_date')->nullable();
            $table->text('approval_note')->nullable();
            $table->text('rejection_note')->nullable();
            $table->text('modification_note')->nullable();

            // Tracking
            $table->enum('status', [
                'pending',            // Submitted, awaiting meeting
                'in_agenda',          // Added to meeting agenda
                'approved',           // Approved by parliament, awaiting payment
                'rejected',           // Rejected
                'modification_needed', // Sent back for changes
                'active',             // Paid out, investment running
                'matured',            // Duration elapsed, awaiting settlement
                'closed',             // Fully settled
            ])->default('pending');

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ─── Meeting Agenda Items ────────────────────────────────────────
        Schema::create('investment_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('meeting_date');
            $table->string('venue')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'held', 'cancelled'])->default('scheduled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('investment_meeting_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('agenda_order')->default(0);
            // Decision made at meeting
            $table->enum('decision', ['pending', 'approved', 'rejected', 'modification_needed'])->default('pending');
            $table->text('decision_note')->nullable();
            $table->timestamps();
            $table->unique(
                ['investment_meeting_id', 'investment_request_id'],
                'inv_meeting_req_unique'
            );
        });

        // ─── Investment Payments (Disbursement) ──────────────────────────
        Schema::create('investment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('voucher_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank', 'bkash', 'nagad', 'cheque'])->default('cash');
            $table->string('reference')->nullable();      // bank ref / cheque no
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ─── Investment Settlements ──────────────────────────────────────
        Schema::create('investment_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('voucher_number')->unique();

            // Financials
            $table->decimal('investment_amount', 12, 2);
            $table->decimal('actual_profit_loss', 12, 2)->default(0); // positive=profit, negative=loss
            $table->enum('outcome', ['profit', 'loss', 'breakeven'])->default('breakeven');
            $table->decimal('return_amount', 12, 2);     // what member gets back

            // Payment
            $table->enum('payment_method', ['cash', 'bank', 'bkash', 'nagad', 'cheque'])->default('cash');
            $table->string('reference')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('settlement_date');
            $table->text('note')->nullable();
            $table->foreignId('settled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_settlements');
        Schema::dropIfExists('investment_payments');
        Schema::dropIfExists('investment_meeting_items');
        Schema::dropIfExists('investment_meetings');
        Schema::dropIfExists('investment_requests');
    }
};
