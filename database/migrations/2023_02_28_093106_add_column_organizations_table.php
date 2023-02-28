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
        Schema::table('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('vanity_slug')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('website')->nullable();
            $table->text('about')->nullable();
            $table->integer('category')->nullable();
            $table->text('vanity_link')->nullable();
            $table->enum('status', ['0', '1'])->nullable()->default('0');
            $table->integer('magnet_community_id')->nullable();
            $table->text('associat_lab')->nullable();
            $table->text('associat_challenges')->nullable();
            $table->string('plan')->nullable();
            $table->string('plan_validity')->nullable();
            $table->integer('labs_limit')->nullable();
            $table->integer('challenges_limit')->nullable();
            $table->integer('resources_limit')->nullable();
            $table->tinyInteger('is_auto_created')->nullable();
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
        Schema::table('organizations', function (Blueprint $table) {
            //
        });
    }
};
