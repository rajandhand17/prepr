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
        Schema::create('template_lab_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_lab_id');
            $table->string('achievement_name');
            $table->integer('achievement_points');
            $table->json('achievement_condition');
            $table->text('achievement_image');
            $table->foreign('template_lab_id')->references('id')->on('template_labs')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_lab_achievements');
    }
};
