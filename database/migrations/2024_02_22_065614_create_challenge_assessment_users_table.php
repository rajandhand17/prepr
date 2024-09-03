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
        Schema::create('challenge_assessment_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criteria_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('score');
            $table->longText('comment')->nullable();
            $table->longText('criteria_comment')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('0-> draft, 1-> published');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('criteria_id')->references('id')->on('challenge_assessment_criterias')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_assessment_users');
    }
};
