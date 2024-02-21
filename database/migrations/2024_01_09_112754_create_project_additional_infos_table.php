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
        Schema::create('project_additional_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->unsignedBigInteger('verticals_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('industry_id')->references('id')->on('project_industries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('verticals_id')->references('id')->on('project_verticals')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('type_id')->references('id')->on('project_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('stage_id')->references('id')->on('project_stages')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('project_status')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_additional_info');
    }
};
