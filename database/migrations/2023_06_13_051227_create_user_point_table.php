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
        Schema::create('user_point', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->enum('type', ['facebook', 'google', 'linkedin', 'lab_join', 'login', 'create_thread', 'reply_thread', 'submit_project', 'submit_success', 'vote_project', 'create_project', 'referal_code', 'referal_user', 'add_member', 'voter_project', 'challengeTrophy', 'challenge_participation'])->default('login');
            $table->integer('point')->nullable();
            $table->dateTime('date')->nullable();
            $table->enum('status', ['0', '1'])->default('1')->comment('0-> yes, 1-> no');
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
        Schema::dropIfExists('user_point');
    }
};
