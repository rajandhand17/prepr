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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('organisation');
            $table->integer('category');
            $table->string('challenge_id')->nullable();
            $table->string('lab_id')->nullable();
            $table->string('resource_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('group_image');
            $table->enum('privacy', ['public', 'private'])->default('public');
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
        Schema::dropIfExists('groups');
    }
};
