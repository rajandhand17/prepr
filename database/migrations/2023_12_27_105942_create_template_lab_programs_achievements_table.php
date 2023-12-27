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
        Schema::create('template_lab_programs_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_lab_program_id');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('template_lab_program_id','tlp_achievements_template_lab_program_id_foreign')->references('id')->on('template_lab_program')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_lab_programs_achievements');
    }
};
