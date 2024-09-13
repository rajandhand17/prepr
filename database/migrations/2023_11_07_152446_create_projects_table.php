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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('is_view_enabled', ['0', '1'])->default('0')->comment('0 -> no, 1 -> yes, Allow users outside your team to view your project.');
            $table->enum('is_download_enabled', ['0', '1'])->default('0')->comment('0 -> no, 1 -> yes, Allow users outside your team to download your project files');
            $table->enum('media_type', ['0', '1', '2'])->default('0')->comment('0 -> Image, 1-> embedded, Type of media defining of media, 2-> Video');
            $table->text('media')->nullable();
            $table->enum('privacy', ['0', '1'])->default('0')->comment('Privacy of Project, 0 -> Public & 1 -> Private');
            $table->enum('is_submitted', ['0', '1'])->default('0')->comment('Project Submission Status, 0 -> Not Submitted & 1 -> Submitted');
            $table->enum('recruiting_status', ['0', '1'])->default('0')->comment('0-> Currently recruiting, 1-> Currently not recruiting');
            $table->integer('total_share')->nullable();
            $table->unsignedBigInteger('challenge_id')->nullable();
            $table->unsignedBigInteger('lab_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
