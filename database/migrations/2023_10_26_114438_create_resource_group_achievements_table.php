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
        Schema::create('resource_group_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_group_id');
            $table->string('achievement_name', 255)->nullable();
            $table->string('achievement_points', 255)->nullable();
            $table->text('achievement_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('resource_group_id')->references('id')->on('resource_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_group_achievements');
    }
};
