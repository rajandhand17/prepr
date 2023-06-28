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
            $table->string('title');
            $table->longText('description');
            $table->enum('privacy', ['0', '1'])->comment("0->yes,1->no");
            $table->string('media_type')->default('image');
            $table->text('media')->nullable();
            $table->enum('status', ['0', '1','2'])->default('1')->comment("0=>draft,1=>published,2=>archive");
            $table->integer('total_share')->nullable();
            $table->tinyInteger('is_auto_created')->comment('0=yes,1=no');
            $table->enum('is_resource_sequential', ['0', '1'])->default('1')->comment('0=>yes,1=>no');
            $table->enum('is_sequential', ['0', '1'])->default('1')->comment('0=>yes,1=>no');
            $table->enum('is_achievement_enabled', ['0', '1'])->default('1')->comment('0=>yes,1=>no');
            $table->enum('is_notification_enabled', ['0', '1'])->default('1')->comment('0=>yes,1=>no');
            $table->enum("is_verified",['0','1'])->default('1')->comment("0->verified and 1 for not-verify");
            $table->string("uuid");
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
