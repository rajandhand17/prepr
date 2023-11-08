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
            $table->enum('view_enabled', ['yes', 'no'])->default('yes')->comment('Allow users outside your team to view your project.');
            $table->enum('download_enabled', ['yes', 'no'])->default('yes')->comment('Allow users outside your team to download your project files');
            $table->enum('media_type', ['image', 'embedded', 'video'])->default('image');
            $table->text('media')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('Privacy of Project, 0 -> Public & 1 -> Private');
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedBigInteger('vertical_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
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
