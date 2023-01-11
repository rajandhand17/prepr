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
        Schema::create('lab_social_links', function (Blueprint $table) {
            $table->id();
            $table->string("user_id",191)->nullable();
            $table->string("lab_id",191)->nullable();
            $table->string("social_link_id",191)->nullable();
            $table->string("link_url",191)->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->index("user_id");
            $table->index("lab_id");
            $table->index("social_link_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_social_links');
    }
};
