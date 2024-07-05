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
        Schema::create('auto_create_templates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("role_type");
            $table->enum('user_type', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26'])->comment('0->employee,1->investor,2->teacher,3->job_seeker,4->student,5->recent_grad,6->expert,7->employer,8->Recent Grad,9->facilitator,10->Job Seeker,11->startup,12->learner,13->mentor,14->innovator,15->aspiring_entrepreneur,16->evaluator,17->small,18->intrapreneur,19->ngo,20->enterprise,21->applicant,22->educational,23->community,24->educator,25->government,26->others')->nullable();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('lab_program_id');
            $table->unsignedBigInteger('challenge_path_id');
            $table->enum('invite_labs',['0','1'])->default('0');
            $table->enum('invite_challenges',['0','1'])->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('lab_program_id')->references('id')->on('lab_programs')->onDelete('cascade');
            $table->foreign('challenge_path_id')->references('id')->on('challenge_paths')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_create_templates');
    }
};
