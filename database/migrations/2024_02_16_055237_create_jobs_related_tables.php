<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('fr_CA_title');
            $table->string('lc_id')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('user_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('job_id');
            $table->boolean('pinned')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('related_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('related_job_id');
            $table->foreign('job_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->foreign('related_job_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('skill_id');
            $table->foreign('job_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_jobs');
        Schema::dropIfExists('user_jobs');
        Schema::dropIfExists('job_skills');
        Schema::dropIfExists('job_titles');
    }
};