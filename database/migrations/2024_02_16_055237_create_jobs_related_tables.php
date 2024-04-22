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
        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('title')->unique();
            $table->string('fr_CA_title');
            $table->string('lightcast_id')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_job_titles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('job_title_id');
            $table->boolean('pinned')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('related_job_titles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_title_id');
            $table->unsignedBigInteger('related_job_title_id');
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->foreign('related_job_title_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_title_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_title_id');
            $table->unsignedBigInteger('skill_id');
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
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
        Schema::dropIfExists('related_job_titles');
        Schema::dropIfExists('user_job_titles');
        Schema::dropIfExists('job_title_skills');
        Schema::dropIfExists('job_titles');
    }
};
