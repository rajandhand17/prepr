<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_invite_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('inviter_id');
            $table->unsignedBigInteger('role')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['0','1'])->comment("0 -> not-verify, 1-> verify")->default('0');
            $table->enum('invite_type', ['email', 'network', 'other'])->default('network');
            $table->enum('invitation_status', ['auto', 'manual', 'other'])->comment('auto =invite as auto ,manual = invite as ask for permission')->default('auto');
            $table->enum('invite_status', ['invited', 'accepted', 'pending', 'declined'])->default('pending');
            $table->enum('email_status', ['sent', 'fail', 'schedule', 'other'])->default('schedule');
            $table->string("email_responce")->default('null');
            $table->enum('email_resend_status', ['yes', 'no'])->default('no');
            $table->string("subject_line")->nullable();
            $table->text("email_message")->nullable();
            $table->text("fail_schedule")->nullable();
            $table->foreign('organisation_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('inviter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role')->references('id')->on('roles')->onDelete('cascade');
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
        Schema::dropIfExists('organization_invite_users');
    }
};
