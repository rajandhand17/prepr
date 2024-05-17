<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('related_pathways', function (Blueprint $table) {
            $table->id();
            $table->string('lightcast_pathway_id');
            $table->string('related_lightcast_pathway_id');
            $table->string('category');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lightcast_pathway_id')->references('lightcast_pathway_id')->on('job_title_pathways')->onDelete('cascade');
            $table->foreign('related_lightcast_pathway_id')->references('lightcast_pathway_id')->on('job_title_pathways')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_pathways');
    }
};
