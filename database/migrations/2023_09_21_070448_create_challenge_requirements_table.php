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
        Schema::create('challenge_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->integer('min_rank')->comment('minimum rank needed to start challenge')->nullable();
            $table->integer('min_points')->comment('minimum points needed to start challenge')->nullable();
            $table->json('project_submission_requirement_ids')->comment('requirements needed to complete challenge');
            $table->integer('max_project_submission')->comment('maximum limit to project submissions')->nullable();
            $table->integer('min_experience')->comment('minimum experience needed to start challenge')->nullable();
            $table->integer('min_imported_badges')->comment('minimum imported achievement needed to start challenge')->nullable();
            $table->integer('min_achievement_counts')->comment('minimum achievements needed to start challenge')->nullable();
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_requirements');
    }
};
