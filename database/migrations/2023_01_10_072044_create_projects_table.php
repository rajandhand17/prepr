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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string("language",191)->default("en");
            $table->string("slug",191);
            $table->string("user_id",191)->default("0");
            $table->enum("reference_type",['lab', 'challange'])->nullable();
            $table->integer("reference_id")->default(0);
            $table->integer("lab_id")->nullable();
            $table->enum("privacy",['public', 'private'])->default("public");
            $table->enum("file_download_privacy",['public', 'private']);
            $table->string("team",191)->nullable();
            $table->enum("enable_team_chat",["0","1"])->default(0);
            $table->enum("is_alert_sent",["0","1"])->default(0);
            $table->integer("stage")->nullable();
            $table->integer("status")->nullable();
            $table->integer("recruiting_status")->nullable();
            $table->integer("type")->nullable();
            $table->integer("industry")->nullable();
            $table->integer("verticals")->nullable();
            $table->string("media_type")->nullable();
            $table->text("image")->nullable();
            $table->string("title",191);
            $table->text("description");
            $table->date("date")->nullable();
            $table->integer("category")->nullable();
            $table->integer("total_share")->default(0);
            $table->string("user_social_links",191)->nullable();
            $table->text("associate_lab")->nullable();
            $table->string("univercity",191)->nullable();
            $table->string("coworking_space",191)->nullable();
            $table->string("tecnology",191)->nullable();
            $table->string("incubator",191)->nullable();
            $table->string("skills",191)->nullable();
            $table->integer("challenge_id")->nullable();
            $table->set("start_challenge",['no', 'yes', 'complete'])->default('no');
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
        Schema::dropIfExists('projects');
    }
};
