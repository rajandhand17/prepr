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
        Schema::create('project_social_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->enum('vote', ['0', '1'])->default('0')->comment('0 -> no-vote, 1 -> vote');
            $table->enum('like_dislike', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1->like, 2->dislike');
            $table->enum('share', ['0', '1'])->default('0')->comment('0->no-activity, 1->share');
            $table->enum('favourite', ['0', '1', '2'])->default('0')->comment('0->no-activity, 1->favourite, 2->unfavourite');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_social_activities');
    }
};
