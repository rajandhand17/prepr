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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('refence_id');
            $table->enum('refence_type', ['0', '1', '2', '3', '4', '5'])->comment('0->lab,1->project,2->user,3->challange,4->challenge-group,5->lab-group')->nullable();
            $table->enum('is_favorite', ['0', '1'])->default('0')->comment('0->not-favorite,1->favorite');
            $table->enum('is_like', ['0', '1'])->default('0')->comment('0->not-likeit,1->likeit');
            $table->enum('is_follow', ['0', '1'])->default('0')->comment('0->not-follow,1->follow');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('favorites');
    }
};
