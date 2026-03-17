<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Election (annual committee renewal)
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('election_year');
            $table->date('nomination_start');
            $table->date('nomination_end');
            $table->date('voting_start');
            $table->date('voting_end');
            $table->enum('status', ['upcoming','nomination','voting','counting','completed','cancelled'])
                  ->default('upcoming');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Each position being contested
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->string('position_name'); // সভাপতি, সম্পাদক, কোষাধ্যক্ষ etc.
            $table->unsignedTinyInteger('seats')->default(1);
            $table->timestamps();
        });

        // Candidates nominated for a position
        Schema::create('election_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('election_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->text('manifesto')->nullable();
            $table->enum('status', ['nominated','approved','rejected','withdrawn'])->default('nominated');
            $table->timestamps();
            $table->unique(['election_position_id','member_id']);
        });

        // Votes cast by members
        Schema::create('election_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('election_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('election_candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('voter_member_id')->constrained('members')->cascadeOnDelete();
            $table->timestamp('voted_at')->useCurrent();
            $table->unique(['election_position_id','voter_member_id'], 'one_vote_per_position');
        });

        // Elected committee result
        Schema::create('election_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('election_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('election_candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('vote_count')->default(0);
            $table->boolean('is_elected')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_results');
        Schema::dropIfExists('election_votes');
        Schema::dropIfExists('election_candidates');
        Schema::dropIfExists('election_positions');
        Schema::dropIfExists('elections');
    }
};
