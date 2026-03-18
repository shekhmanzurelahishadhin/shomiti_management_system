<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();

            // Personal Info
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->enum('marital_status', ['married', 'unmarried', 'divorced', 'widowed'])->default('unmarried');
            $table->string('nid_or_birth_cert')->nullable();
            $table->string('photo')->nullable();

            // Present Address
            $table->string('present_village')->nullable();
            $table->string('present_post_office')->nullable();
            $table->string('present_union')->nullable();
            $table->string('present_ward')->nullable();
            $table->string('present_upazila')->nullable();
            $table->string('present_district')->nullable();

            // Permanent Address
            $table->string('permanent_village')->nullable();
            $table->string('permanent_post_office')->nullable();
            $table->string('permanent_union')->nullable();
            $table->string('permanent_ward')->nullable();
            $table->string('permanent_upazila')->nullable();
            $table->string('permanent_district')->nullable();

            // Contact
            $table->string('phone')->nullable();

            // Nominee
            $table->string('nominee_name')->nullable();
            $table->string('nominee_father_spouse')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('nominee_phone')->nullable();
            $table->string('nominee_nid_or_birth_cert')->nullable();

            // Membership
            $table->date('join_date');
            $table->decimal('entry_fee', 10, 2)->default(100.00);
            $table->unsignedTinyInteger('share_count')->default(1); // max 2
            $table->decimal('monthly_deposit', 10, 2)->default(1000.00);
            $table->unsignedBigInteger('referred_by_member_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'on_hold', 'disconnected'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
