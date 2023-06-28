<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('language')->default('en');
            $table->integer('user_id');
            $table->integer('organizartion_id');
            $table->integer('category_id');
            $table->string('slug');
            $table->varchar('title');
            $table->longText('description');
            $table->enum('privacy', ['yes', 'no']);
            $table->string('mediaType')->default('image');
            $table->text('media')->nullable();
            $table->double('latitute')->nullable();
            $table->double('longitude')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->enum('status', ['0', '1'])->default('1');
            $table->integer('total_share')->nullable();
            $table->tinyInteger('is_auto_created')->comment('0=No,1=Yes');
            $table->enum('is_resource_sequential', ['yes', 'no'])->default('no')->comment('1 if lab resources is set to sequential by user');
            $table->enum('is_challenge_sequential', ['yes', 'no'])->default('no')->comment('1 if lab challenges is set to sequential by user');
            $table->enum('is_achievement_enabled', ['yes', 'no'])->default('no')->comment('1 if lab achievements is enabled or not by user');
            $table->enum('is_notification_enabled', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('labs');
    }
};
