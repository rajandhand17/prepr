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
        Schema::create('challenge_template_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->enum('assessment_type', ['0', '1', '2'])->comment('0->none assessment, 1->open assessment,2->closed assessment');
            $table->enum('visibility', ['0', '1', '2'])->comment('0->visible to non, 1->visible to users,2->hidden visibility');
            $table->string('members_email')->nullable()->comment('for closed assessment type email is mandatory else it can be nullable');
            $table->text('guidelines')->nullable()->comment('Guidelines for assessment');
            $table->text('attachments')->nullable()->comment('Attachments if available for assessment');
            $table->foreign('challenge_template_id')->references('id')->on('challenge_templates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_template_assessments');
    }
};
