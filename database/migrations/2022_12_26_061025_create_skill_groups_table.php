<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fr_CA_title')->nullable();
            $table->string('description')->nullable();
            $table->string('fr_CA_description')->nullable();
            $table->json('skills');
            $table->json('skill_stacks');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_groups');
    }
};
