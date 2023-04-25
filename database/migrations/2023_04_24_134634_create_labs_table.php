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
            $table->string("language");
            $table->string("slug");
            $table->integer("user_id");
            $table->integer("organisation");
            $table->string('title');
            $table->enum("verification",["0","1"])->default("0")->comment("0 verified, 1 for not verified");
            $table->longText("description");
            $table->integer('category')->default(null);
            $table->enum("privacy",['public', 'private'])->default("public");
            $table->text("media_type")->default("image");
            $table->double('latitute')->default(null);
            $table->double('longitude')->default(null);
            $table->text("address")->default(null);
            $table->string("city")->default(null);
            $table->string("country")->default(null);
            $table->enum("challnges",["0","1"])->default(0);
            $table->longText('lab_skills')->default(null);
            $table->text("tag")->default(null);
            $table->enum("status",["0","1"])->default("1");
            $table->string("phone")->default(null);
            $table->string("company")->default(null);
            $table->string("email")->default(null);
            $table->string("website")->default(null);
            $table->string("facebook")->default(null);
            $table->string("linked")->default(null);
            $table->string("twitter")->default(null);
            $table->string("total_share")->default(null);
            $table->string("user_count")->default(null);
            $table->tinyInteger("is_auto_created")->default("0")->comment("0=No,1=Yes");
            $table->enum("res_sequence",["0","1"])->default("0")->comment("1 if lab resources is set to sequential by user");
            $table->enum("cha_sequence",["0","1"])->default("0")->comment("1 if lab challenges is set to sequential by user");
            $table->enum("enable_achievement",["0","1"])->default("0")->comment("1 if lab achievements is enabled or not by user");
            $table->string("skill_groups")->default(null);
            $table->string("skill_stacks")->default(null);
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
