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
        Schema::create('lab_resources', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lab_id");
            $table->bigInteger("resources_id")->nullable();
            $table->bigInteger("collection_id")->nullable();
            $table->bigInteger("group_id")->nullable();
            $table->enum("status",['0','1']);
            $table->timestamps();
            $table->softDeletes();
            $table->index("lab_id");
            $table->index("resources_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_resources');
    }
};
