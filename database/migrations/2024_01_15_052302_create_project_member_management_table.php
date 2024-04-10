<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_member_management', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('inviter_id')->comment('Sender Id of inviter');
            $table->string('email')->nullable()->comment('Receiver Email ID');
            $table->text('invitee_name')->nullable();
            $table->enum('invite_type', ['0', '1', '2', '3'])->comment('0-> Email, 1-> Network, 2-> Csv, 3-> Link');
            $table->enum('invite_status', ['0', '1', '2', '3'])->default('2')->comment('0-> invited, 1-> accepted, 2-> pending, 3-> declined');
            $table->enum('email_status', ['0', '1', '2', '3'])->default('0')->comment('0-> scheduled, 1->  sent, 2-> fail, 3 ->  NA');
            $table->string('email_response')->nullable();
            $table->enum('email_resend_status', ['0', '1'])->default('1')->comment('0-> no, 1-> yes');
            $table->enum('inviter_access_level', ['0', '1', '2'])->default('0')->comment('0-> Viewer, 1-> Editor, 2-> Team Leader');
            $table->string('subject_line')->nullable();
            $table->text('email_body')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('inviter_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_member_management');
    }
};
