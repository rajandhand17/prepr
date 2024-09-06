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
        Schema::create('project_pitch_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('pitch_template_id')->nullable();
            $table->unsignedBigInteger('project_pitch_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('pitch_template_id')->references('id')->on('pitch_templates')->onDelete('cascade');
            $table->foreign('project_pitch_id')->references('id')->on('challenge_pitches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_pitch_values');
    }
};
