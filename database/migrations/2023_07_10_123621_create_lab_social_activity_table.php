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
        Schema::create('lab_social_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lab_id');
            $table->enum('like_dislike', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1=>like,2=>dislike');
            $table->enum('follow_unfollow', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1=>follow,2=>unfollow');
            $table->enum('favourite', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1=>favourite,2=>unfavored');
            $table->unsignedBigInteger('share')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('share')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
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
        Schema::dropIfExists('lab_social_activity');
    }
};
