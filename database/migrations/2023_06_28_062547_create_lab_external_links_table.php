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
        Schema::create('lab_external_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->string('social_media_link');
            $table->unsignedBigInteger('social_link_id');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('social_link_id')->references('id')->on('social_links')->onDelete('cascade');
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
        Schema::dropIfExists('lab_external_links');
    }
};
