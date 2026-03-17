<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('total_fund', 12, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('contribution_type', ['full', 'half', 'quarter'])->default('full');
            $table->unsignedInteger('draw_order')->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['committee_id', 'member_id']);
        });

        Schema::create('committee_draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('draw_order');
            $table->date('draw_date')->nullable();
            $table->decimal('payout_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_draws');
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('committees');
    }
};
