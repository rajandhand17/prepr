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
        Schema::create('template_challenge_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_challenge_id');
            $table->enum('achievement_type', ['0', '1'])->comment('0->participation, 1->incentive')->default('0');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_prize', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->foreign('template_challenge_id')->references('id')->on('template_challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_achievements');
    }
};
