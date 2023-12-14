<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('template_challenge_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('template_challenge_id');
            $table->integer('min_rank')->comment('minimum rank needed to start challenge')->nullable();
            $table->integer('min_points')->comment('minimum points needed to start challenge')->nullable();
            $table->json('project_submission_requirement_ids')->comment('requirements needed to complete challenge');
            $table->integer('max_project_submission')->comment('maximum limit to project submissions')->nullable();
            $table->integer('max_project_associate')->comment('maximum limit to project associated')->nullable();
            $table->integer('min_experience')->comment('minimum experience needed to start challenge')->nullable();
            $table->integer('min_imported_badges')->comment('minimum imported achievement needed to start challenge')->nullable();
            $table->integer('min_achievement_counts')->comment('minimum achievements needed to start challenge')->nullable();
            $table->enum('allow_submit_project', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->enum('requirement_program', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->enum('complete_education_program', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->enum('complete_experience', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->text('additional_requirements')->comment('Additional requirements if any')->nullable();
            $table->foreign('template_challenge_id','fk_template_challenge_requirements')->references('id')->on('template_challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_requirements');
    }
};
