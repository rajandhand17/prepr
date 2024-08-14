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
        Schema::create('skills_activity_awards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fr_CA_name')->nullable();
            $table->integer('skill');
            $table->string('description')->nullable();
            $table->text('fr_CA_description')->nullable();
            $table->string('image');
            $table->integer('points');
            $table->integer('challenge_participation_awards')->nullable();
            $table->integer('challenge_win_awards')->nullable();
            $table->integer('challenge_path_awards')->nullable();
            $table->integer('lab_program_awards')->nullable();
            $table->integer('resource_group_awards')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills_activity_awards');
    }
};
