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
        Schema::create('job_title_pathways', function (Blueprint $table) {
            $table->id();
            $table->string('lightcast_pathway_id')->unique();
            $table->string('name');
            $table->string('fr_CA_name')->nullable();
            $table->integer('job_level');
            $table->integer('mean_salary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_title_pathways');
    }
};
