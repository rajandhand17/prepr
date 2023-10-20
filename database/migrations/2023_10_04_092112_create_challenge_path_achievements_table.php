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
        Schema::create('challenge_path_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_path_id');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('challenge_path_id')->references('id')->on('challenge_paths')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_path_achievements');
    }
};
