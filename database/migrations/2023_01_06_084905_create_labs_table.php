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
            $table->bigIncrements('id');
            $table->string('language',191);
            $table->string('slug',191);
            $table->integer('user_id');
            $table->integer('organisation');
            $table->string('title');
            $table->enum('verification', ['0','1'])->default('0');
            $table->text('description');
            $table->integer('category')->nullable();
            $table->enum('privacy', ['public', 'private'])->default('public');
            $table->string("media_type")->default("image");
            $table->string('image')->nullable();
            $table->text('member')->nullable();
            $table->enum('member_type', ['member', 'moderator'])->nullable();
            $table->double('latitute')->nullable();
            $table->double('longitude')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->enum("challnges",['0', '1'])->default(0);
            $table->longText("lab_skills")->nullable();
            $table->text('tag')->nullable();
            $table->string('tags')->nullable();
            $table->enum('status', ['0','1'])->default('1');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linked')->nullable();
            $table->string('twitter')->nullable();
            $table->integer('total_share')->default('0');
            $table->integer('user_count')->default(0);
            $table->tinyInteger('is_auto_created')->default(0);
            $table->enum('res_sequence', ['0','1'])->default(0);
            $table->enum('cha_sequence', ['0','1'])->default(0);
            $table->enum('enable_achievement', ['0','1'])->default(0);
            $table->enum('skill_groups', ['0','1'])->nullable();
            $table->enum('skill_stacks', ['0','1'])->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id']);
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
