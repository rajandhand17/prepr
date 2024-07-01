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
        Schema::create('community_trophy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('fr_CA_name')->nullable();
            $table->text('image')->nullable();
            $table->string('badge_type')->nullable();
            $table->text('description')->nullable();
            $table->text('fr_CA_description')->nullable();
            $table->string('criteria')->nullable();
            $table->string('issuer')->nullable();
            $table->integer('fb_point')->nullable();
            $table->integer('google_point')->nullable();
            $table->integer('linked_point')->nullable();
            $table->integer('login_point')->nullable();
            $table->integer('create_project_point')->nullable();
            $table->integer('join_lab_point')->nullable();
            $table->integer('submit_project_point')->nullable();
            $table->integer('success_submit_project_point')->nullable();
            $table->integer('add_member_point')->nullable();
            $table->integer('vote_project_point')->nullable();
            $table->integer('reply_chat_point')->nullable();
            $table->integer('create_chat_point')->nullable();
            $table->integer('challenge_participation_awards')->nullable();
            $table->integer('challenge_win_awards')->nullable();
            $table->integer('challenge_path_awards')->nullable();
            $table->integer('lab_program_awards')->nullable();
            $table->integer('resource_group_awards')->nullable();
            $table->string('points')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_trophy');
    }
};
