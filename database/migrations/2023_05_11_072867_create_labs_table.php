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
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('language')->default('en');
            $table->string('slug');
            $table->integer('user_id');
            $table->integer('organisation');
            $table->string('title');
            $table->enum('verification', ['0', '1']);
            $table->text('description');
            $table->integer('category');
            $table->enum('privacy', ['public', 'private']);
            $table->string('mediaType')->default('image');
            $table->text('image')->nullable();
            $table->text('member')->nullable();
            $table->enum('member_type', ['member', 'moderator'])->nullable();
            $table->double('latitute')->nullable();
            $table->double('longitude')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->enum('challnges', ['0', '1'])->default(0);
            $table->longText('lab_skills')->nullable();
            $table->text('tag')->nullable();
            $table->enum('status', ['0', '1'])->default('1');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linked')->nullable();
            $table->integer('total_share')->nullable();
            $table->string('twitter')->nullable();
            $table->integer('user_count')->nullable();
            $table->tinyInteger('is_auto_created')->comment('0=No,1=Yes');
            $table->enum('res_sequence', ['1', '0'])->default('0')->comment('1 if lab resources is set to sequential by user');
            $table->enum('cha_sequence', ['1', '0'])->default('0')->comment('1 if lab challenges is set to sequential by user');
            $table->enum('enable_achievement', ['1', '0'])->default('0')->comment('1 if lab achievements is enabled or not by user');
            $table->string('skill_groups')->nullable();
            $table->string('skill_stacks')->nullable();
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
