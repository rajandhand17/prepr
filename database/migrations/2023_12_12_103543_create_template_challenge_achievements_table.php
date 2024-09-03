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
        Schema::create('challenge_template_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->enum('achievement_type', ['0', '1'])->comment('0->participation, 1->incentive')->default('0');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_prize', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->foreign('challenge_template_id')->references('id')->on('challenge_templates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_template_achievements');
    }
};
