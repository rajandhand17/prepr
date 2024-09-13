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
            $table->text('about')->nullable();
            $table->enum('gender', ['0', '1', '2', '3'])->comment('0->male,1->female,2->other,3->decline_to_answer')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('purpose', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'])->comment('0->looking_teams,1->currently_mentor,2->looking_employers,3->currently_team,4->looking_teammates,5->looking_employees,6->looking_invest,7->looking_mentor,8->looking_for_investors,9->looking_to_create_social_impact,10->looking_to_learn,11->looking_to_solve_problems,12->looking_to_build_skills')->nullable();
            $table->enum('user_type', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26'])->comment('0->employee,1->investor,2->teacher,3->job_seeker,4->student,5->recent_grad,6->expert,7->employer,8->Recent Grad,9->facilitator,10->Job Seeker,11->startup,12->learner,13->mentor,14->innovator,15->aspiring_entrepreneur,16->evaluator,17->small,18->intrapreneur,19->ngo,20->enterprise,21->applicant,22->educational,23->community,24->educator,25->government,26->others')->nullable();
            $table->enum('recent_immigrant', ['1', '2'])->comment('1 -> yes,2 -> no')->default('2');
            $table->enum('indigenous_group', ['1', '2'])->comment('1 -> yes,2->no')->default('2');
            $table->enum('visible_minority', ['1', '2'])->comment('1 -> yes,2 -> no')->default('2');
            $table->enum('disability', ['1', '2'])->comment('1 -> yes,2->no')->default('2');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
