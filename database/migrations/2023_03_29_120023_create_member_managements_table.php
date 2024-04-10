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
        Schema::create('member_management', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['0', '1', '2'])->comment('0-> invite, 1-> join_request, 2-> auto_created');
            $table->enum('invite_type', ['0', '1', '2', '3'])->comment('0-> email, 1-> network, 2-> join_request, 3-> csv');
            $table->bigInteger('module_id');
            $table->enum('module_type', ['0', '1', '2', '3'])->comment('0-> organisation, 1-> lab, 2-> challenge, 3-> project');
            $table->bigInteger('inviter_id')->comment('if join request auto add the component user id');
            $table->string('role')->nullable();
            $table->enum('invite_status', ['0', '1', '2', '3', '4'])->default('2')->comment('0-> invited, 1-> accepted, 2-> pending, 3-> declined, 4->  auto_created');
            $table->string('email')->nullable();
            $table->enum('email_status', ['0', '1', '2', '3'])->default('0')->comment('0-> scheduled, 1->  sent, 2-> fail, 3 ->  NA');
            $table->string('email_response')->nullable();
            $table->enum('email_resend_status', ['0', '1'])->default('1')->comment('0-> no, 1-> yes');
            $table->integer('email_resend_count')->default(0);
            $table->string('subject_line')->nullable();
            $table->text('email_body')->nullable();
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
        Schema::dropIfExists('member_management');
    }
};
