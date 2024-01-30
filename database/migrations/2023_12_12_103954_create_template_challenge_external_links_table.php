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
        Schema::create('challenge_template_external_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->string('social_media_link');
            $table->unsignedBigInteger('social_link_id');
            $table->foreign('challenge_template_id')->references('id')->on('challenge_templates')->onDelete('cascade');
            $table->foreign('social_link_id', 'fk_challenge_template_external_links')->references('id')->on('social_links')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_template_external_links');
    }
};
