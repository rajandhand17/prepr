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
        Schema::create('user_personal_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('about')->nullable();
            $table->enum('gender', ['male', 'female', 'transgender'])->nullable();
            $table->string('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('purpose', ['looking_team', 'currently_mentor', 'looking_employers', 'currently_team', 'looking_teammates', 'looking_employees', 'looking_invest', 'looking_mentor', 'looking_for_investors', 'looking_to_create_social_impact', 'looking_to_learn', 'looking_to_solve_problems', 'looking_to_build_skills']);
            $table->enum('user_type', ['employee', 'investor', 'teacher', 'job_seeker', 'student', 'recent_grad', 'expert', 'employer', 'Recent Grad', 'facilitator', 'Job Seeker', 'startup', 'learner', 'mentor', 'innovator', 'aspiring_entrepreneur', 'evaluator', 'small_mid_size_business', 'intrapreneur', 'ngo_charity_not_for_profit', 'enterprise', 'applicant', 'educational_institution', 'community_organization']);
            $table->enum('recent_immigrant', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->enum('indigenous_group', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->enum('visible_minority', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->enum('disability', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
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
        Schema::dropIfExists('user_personals');
    }
};
