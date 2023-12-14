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
        Schema::create('template_challenge_project_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_challenge_id');
            $table->integer('template_id')->nullable();
            $table->foreign('template_challenge_id', 'fk_template_challenge_project_templates')->references('id')->on('template_challenges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_project_templates');
    }
};
