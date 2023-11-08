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
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_template_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('task_template_id')->references('id')->on('pitch_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
