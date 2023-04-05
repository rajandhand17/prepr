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
            $table->id();
            $table->enum('invite_type', ['0', '1', '2'])->comment('0=>email,1=>network,2=>other');
            $table->bigInteger('module_id');
            $table->enum('module_type', ['0', '1', '2', '3'])->comment('0=>lab,1=>challenge,2=>project,3=>other');
            $table->bigInteger('inviter_id');
            $table->bigInteger('invitee_id')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->enum('invite_status', ['0', '1', '2', '3'])->default('2')->comment('0=>invited,1=>accepted,2=>pending,3=>declined');
            $table->string('email')->nullable();
            $table->enum('email_status', ['0', '1', '2', '3'])->default('2')->comment('0=>sent,1=>fail,2=>schedule,3=>other');
            $table->string('email_responce')->nullable();
            $table->enum('email_resend_status', ['0','1'])->default('1')->comment('0=>yes,1=>no');
            $table->string('subject_line')->nullable();
            $table->text('email_message')->nullable();
            $table->text("fail_schedule")->nullable();
            $table->enum('is_exist', ['0','1'])->default('1')->comment('if 0 so this record already exist on before create new member management');
            $table->enum('is_evaluator', ['0','1'])->default(0)->comment(' 1 = challenge eveluator , 0 = invited users ');
            $table->enum('is_join_request', ['0','1'])->default(0)->comment(' 1 = Private lab join Request , 0 = invited users');
            $table->enum('join_request_status', ['0','1','2','3'])->default(0)->comment(' Private lab join Request for 0 = requested ,1 = accepted , 2 = rejected 3 = canceled');
            $table->bigInteger('lab_users_id')->nullable()->comment('lab_users table id');
            $table->enum('privacy', ['0','1','2'])->default(2)->comment(' 0 = private, 1 = public  , 2 = not aplicable');
            $table->enum('auto_invite_status', ['0', '1', '2'])->default('0')->comment('0=auto,1=manual,2=other');
            $table->string('assign_role')->default('user');
            $table->enum('is_auto_created', ['0', '1'])->default(0)->comment('1=yes,0=no,this field use for auto created user');
            $table->enum('user_status', ['0', '1'])->default(1)->comment('1=active,0=deleted,this field use for auto created user');
            $table->index(['inviter_id']);
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
        Schema::dropIfExists('member_managements');
    }
};
