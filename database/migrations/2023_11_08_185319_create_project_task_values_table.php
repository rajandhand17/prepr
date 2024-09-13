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
        Schema::create('project_task_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_template_id')->nullable();
            $table->unsignedBigInteger('project_task_id')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('0 -> Imcompleted, 1 -> Completed');
            $table->string('completed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_template_id')->references('id')->on('pitch_templates')->onDelete('cascade');
            $table->foreign('project_task_id')->references('id')->on('challenge_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_values');
    }
};
