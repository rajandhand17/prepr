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
        Schema::create('pre_built_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fr_CA_title')->nullable();
            $table->integer('points');
            $table->string('component_type');
            $table->string('achievement_image')->nullable();
            $table->enum('achievement_type', ['0','1','2'])->default('0')->comment('0 ->None, 1 ->Participation Achievement, 2 -> Winner Achievement');
            $table->enum('status', ['0', '1'])->default('1')->comment('0 ->inactive, 1 ->active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_built_achievements');
    }
};
