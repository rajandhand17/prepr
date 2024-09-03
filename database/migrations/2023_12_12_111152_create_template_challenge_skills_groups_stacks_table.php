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
        Schema::create('challenge_template_skills_groups_stacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1', '2'])->comment('0->skills, 1->group,2->stack');
            $table->foreign('challenge_template_id', 'fk_challenge_template_skills_groups_stacks')->references('id')->on('challenge_templates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_template_skills_groups_stacks');
    }
};
