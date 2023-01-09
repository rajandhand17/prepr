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
        Schema::create('challanges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language')->default("en");
            $table->string('slug')->nullable();
            $table->string('user_id')->default(0)->nullable();
            $table->integer('organisation')->nullable();
            $table->string('host_id')->nullable();
            $table->string('title')->nullable();
            $table->enum("verification",['0','1'])->default(0);
            $table->text('description')->nullable();
            $table->string('sourcelink')->nullable();
            $table->text('category')->nullable();
            $table->text('challange_skill')->nullable();
            $table->text('challange_tag')->nullable();
            $table->text('tags')->nullable();
            $table->string('associat_lab')->nullable();
            $table->enum('status', ['open', 'closed', 'completed'])->nullable()->default('open');
            $table->enum('project_privacy', ['public', 'private'])->nullable()->default('public');
            $table->dateTime('deadline')->nullable();
            $table->enum('dates', ['restricted', 'flexible'])->default('restricted')->comment('date type restricted/flexible');
            $table->integer('flexibleDateNumber')->nullable();
            $table->string('flexibleExpireDateDuration')->nullable();
            $table->dateTime('flexibleExpireDate')->nullable();
            $table->text('automaticAlert')->nullable()->comment('set automatic alert to send notification');
            $table->text('submission_deadline_date_desc')->nullable();
            $table->text('min_ranks')->nullable();
            $table->text('min_points')->nullable();
            $table->text('projectSubmissionRequirements')->nullable()->comment('Project Submission Requirements');
            $table->string('submitProject')->nullable()->comment('Allow Submission of Previously Submitted Projects');
            $table->integer('maxProjectSubmission')->default('1000')->comment('Maximum Numbers of Project Submissions');
            $table->integer('maxAssociatedProjects')->default('1000')->comment('Maximum Numbers of Associated Projects');
            $table->string('completeEducationProgram')->nullable()->comment('Completed Education Profile');
            $table->string('completeExperience')->nullable()->comment('Completed Experience Profile');
            $table->text('requirementProgram')->nullable()->comment('Requirement Program');
            $table->integer('minExperience')->default('0')->nullable()->comment('Minimum Numbers of Years Experience');
            $table->integer('minImportedBadges')->default('0')->nullable()->comment('Minimum Numbers of Imported Badges');
            $table->integer('minAchievementTrophies')->default('0')->nullable()->comment('Minimum Numbers of Achievement Trophies');
            $table->text('additional_info')->nullable();
            $table->dateTime('application_deadline')->nullable();
            $table->string('length')->nullable();
            $table->dateTime('last_registration_date')->nullable();
            $table->dateTime('call_date')->nullable();
            $table->text('mediaType')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('privacy', ['public', 'private'])->nullable()->default('public');
            $table->string('total_share')->default(0);
            $table->enum('is_completed', ['0', '1'])->default('1')->comment("'0'=>not complete ,'1'=>complete");
            $table->text('agreement')->nullable();
            $table->enum('type', ['free', 'paid', 'collection'])->nullable()->default('free');
            $table->integer('price')->nullable();
            $table->string('code')->nullable();
            $table->text('subject_line')->nullable();
            $table->text('email_message')->nullable();
            $table->string('application_dateline_desc')->nullable();
            $table->string('call_date_desc')->nullable();
            $table->string('last_registration_date_desc')->nullable();
            $table->dateTime('assessment_date')->nullable();
            $table->string('assessment_date_desc')->nullable();
            $table->enum('published', ['published', 'draft'])->nullable()->default('published');
            $table->text('cover_image')->nullable()->change();
            $table->string('mediaType')->default('image')->change();
            $table->text('application_dateline_desc')->change();
            $table->text('call_date_desc')->change();
            $table->text('last_registration_date_desc')->change();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id']);
            $table->index(['organisation']);
            $table->index(['host_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challanges');
    }
};
