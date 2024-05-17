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
        Schema::create('challenge_job_title_association', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('challenge_id')->unsigned();
            $table->bigInteger('job_title_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('challenge_id')
                ->references('id')->on('challenges')
                ->onDelete('cascade');

            $table->foreign('job_title_id')
                ->references('id')->on('job_titles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_job_title_association');
    }
};
