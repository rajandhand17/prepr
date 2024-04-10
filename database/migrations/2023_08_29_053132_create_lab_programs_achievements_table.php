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
        Schema::create('lab_programs_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_program_id');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lab_program_id')->references('id')->on('lab_programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_programs_achievements');
    }
};
