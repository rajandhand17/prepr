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
        Schema::create('challenge_template_assessment_criterias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->unsignedBigInteger('template_assessment_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('score');
            $table->integer('weight');
            $table->foreign('challenge_template_id', 'fk_challenge_template_assessment_criterias')->references('id')->on('challenge_templates')->onDelete('cascade');
            $table->foreign('template_assessment_id', 'fk_challenge_template_assessment')->references('id')->on('challenge_template_assessments')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_template_assessment_criterias');
    }
};
