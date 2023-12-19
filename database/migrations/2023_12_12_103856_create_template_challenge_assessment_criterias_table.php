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
        Schema::create('template_challenge_assessment_criterias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_challenge_id');
            $table->string('title');
            $table->integer('score');
            $table->integer('weight');
            $table->foreign('template_challenge_id', 'fk_template_challenge_criterias')
                ->references('id')
                ->on('template_challenges')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_assessment_criterias');
    }
};
