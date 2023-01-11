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
            $table->integer("organisation_id");
            $table->integer("user_id")->nullable();
            $table->bigInteger("inviter_id");
            $table->text("challenge_ids")->nullable();
            $table->text("lab_ids")->nullable();
            $table->text("resource_ids")->nullable();
            $table->string("role",191)->nullable();
            $table->string("email",191)->nullable();
            $table->enum("status",["0","1"])->default(0);
            $table->enum("invite_type",['email', 'network', 'other'])->default("network");
            $table->enum("invitation_status",['auto', 'manual', 'other'])->default("auto")->comment("auto =invite as auto ,manual = invite as ask for permission");
            $table->enum("invite_status",['invited', 'accepted', 'pending', 'declined'])->default("pending");
            $table->enum("email_status",['sent','fail','schedule','other'])->default("schedule");
            $table->string("email_responce",191)->nullable();
            $table->enum("email_resend_status",['yes','no'])->default("no");
            $table->string("subject_line")->nullable();
            $table->text("email_message")->nullable();
            $table->text("fail_schedule")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index("organisation_id");
            $table->index("user_id");
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
