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
        Schema::create('member_managements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('invite_type', ['email', 'network', 'other']);
            $table->bigInteger('module_id');
            $table->enum('module_type', ['lab', 'challenge', 'project', 'other']);
            $table->bigInteger('inviter_id');
            $table->bigInteger('invitee_id')->nullable();
            $table->string('email')->nullable();
            $table->enum('invite_status', ['invited', 'accepted', 'pending', 'declined'])->default('pending');
            $table->enum('email_status', ['sent', 'fail', 'schedule', 'other'])->default('schedule');
            $table->string('email_responce')->nullable();
            $table->enum('email_resend_status', ['yes','no'])->default('no');
            $table->enum('is_exist', ['yes','no'])->default('no')->comment('if yes so this record already exist on befor create new member management');
            $table->integer('is_evaluator')->default(0)->comment(' 1 = challenge eveluator , 0 = invited users ');
            $table->integer('is_join_request')->default(0)->comment(' 1 = Private lab join Request , 0 = invited users');
            $table->integer('join_request_status')->default(0)->comment(' Private lab join Request for 0 = requested ,1 = accepted , 2 = rejected 3 = canceled');
            $table->bigInteger('lab_users_id')->nullable()->comment('lab_users table id');
            $table->integer('privacy')->default(2)->comment('1 = public , 0 = private , 2 = not aplicable');
            $table->enum('auto_invite_status', ['auto', 'manual', 'other'])->default('auto');
            $table->string('assign_role')->default('user');
            $table->string('subject_line')->nullable();
            $table->text('email_message')->nullable();
            $table->integer('is_auto_created')->default(0)->comment(' 1 = yes , 0 = no , this field use for auto created user');
            $table->integer('user_status')->default(1)->comment(' 1 = active , 0 = deleted , this field use for auto created user');
            $table->index(['inviter_id']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
        Schema::dropIfExists('member_managements');
    }
};
