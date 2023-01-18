<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_personals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('about')->nullable();
            $table->enum("gender",["male","female","transgender"])->nullable();
            $table->date("date_of_birth")->nullable();
            $table->integer("age")->nullable();
            $table->enum("status",["looking_team","currently_mentor","looking_employers","currently_team","Looking_teammates","looking_employees","looking_invest","looking_mentor","looking_for_investors","looking_to_create_social_impact","looking_to_learn","looking_to_solve_problems","looking_to_build_skills"])->nullable();
            $table->enum("user_type",["employee","investor","teacher","job_seeker","student","recent_grad","expert","employer","Recent Grad","facilitator","Job Seeker","startup","learner","mentor","innovator","aspiring_entrepreneur","evaluator","small_mid_size_business","intrapreneur","ngo_charity_not_for_profit","enterprise","applicant","educational_institution","community_organization"])->nullable();
            $table->unsignedBigInteger("language")->default(1);
            $table->tinyInteger("recent_immigrant")->nullable();
            $table->tinyInteger("indigenous_group")->nullable();
            $table->tinyInteger("visible_minority")->nullable();
            $table->tinyInteger("disability")->nullable();
            $table->index("user_id");
            $table->index("language");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('language')->references('id')->on('languages');
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
