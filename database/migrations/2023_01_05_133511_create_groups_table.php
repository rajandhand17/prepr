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
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',191);
            $table->string('title',191);
            $table->text('description');
            $table->integer('organisation');
            $table->integer('category');
            $table->enum('type', ['lab','challenge','resource'])->default('resource');
            $table->string('challenge_id',191)->nullable();
            $table->string('lab_id',191)->nullable();
            $table->string('resource_id',191)->nullable();
            $table->string('collection_id',191)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('group_image',191);
            $table->enum('privacy', ['public','private'])->default('public');
            $table->set('status', ['open','closed'])->default('open');
            $table->set('challenge_privacy', ['public','private'])->default('public');
            $table->set('privacy_project', ['public','private'])->default('public');
            $table->set('published', ['published','draft'])->default('published');
            $table->string('prize')->nullable();
            $table->string('points')->nullable();
            $table->string('trophy')->nullable();
            $table->tinyInteger('is_auto_created')->default(0)->comment('0=No,1=Yes');
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
