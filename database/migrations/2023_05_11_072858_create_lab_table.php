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
            $table->string('slug');
            $table->integer("user_id");
            $table->integer("organisation");
            $table->string("title");
            $table->enum("verification",["0","1"]);
            $table->text("description");
            $table->interger("category");
            $table->enum("privacy",['public', 'private']);
            $table->string("mediaType")->default("image");
            $table->text("image")->default(null);
            $table->text("member")->default(null);
            $table->enum("member_type",['member', 'moderator'])->default(null);
            $table->double("latitute")->default(null);
            $table->double("longitude")->default(null);
            $table->text("address")->default(null);
            $table->string("city")->default(null);
            $table->string("country")->default(null);
            $table->enum("challnges",["0","1"])->default(0);
            $table->longText("lab_skills")->null();
            $table->text("tag")->default("null");
            $table->enum("status",["0","1"])->default("1");
            $table->varchar("phone")->default(null);
            $table->varchar("company")->default(null);
            $table->varchar("email")->default(null);
            $table->varchar("website")->default(null);
            $table->varchar("facebook")->default(null);
            $table->varchar("linked")->default(null);
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
        Schema::dropIfExists('lab');
    }
};
