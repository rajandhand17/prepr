<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('preferred_language')->default('en');
            $table->string('preferred_timezone')->nullable();
            $table->string('preferred_organization')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('two_factor_verification', ['0', '1'])->comment('0 -> disabled, 1 -> enabled')->default('0');
            $table->string('otp')->nullable();
            $table->text('profile_image')->nullable();
            $table->integer('user_points')->nullable();
            $table->integer('user_rank')->nullable();
            $table->integer('achievement_count')->nullable();
            $table->enum('verified_user', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->string('verify_token')->nullable();
            $table->string('referral_code')->nullable();
            $table->enum('is_profile_completed', ['0', '1'])->comment('0 -> incomplete, 1 -> complete')->default('0');
            $table->rememberToken();
            $table->enum('is_deactivated', ['0', '1'])->comment('0->activated, 1->deactivated')->default('0');
            $table->enum('is_onboarding_completed', ['0', '1'])->comment('0-> no, 1-> yes')->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
