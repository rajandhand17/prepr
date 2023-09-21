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
        Schema::create('challenge_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->enum('achievement_type', ['0', '1'])->comment('0->participation, 1->incentive')->default('0');
            $table->string('achievement_name');
            $table->string('achievement_prize');
            $table->integer('achievement_points');
            $table->text('achievement_image');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_achievements');
    }
};
