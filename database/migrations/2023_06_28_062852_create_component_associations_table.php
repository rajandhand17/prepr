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
        Schema::create('component_associations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lab_id')->nullable();
            $table->bigInteger('lab_program_id')->nullable();
            $table->bigInteger('challenge_id')->nullable();
            $table->bigInteger('challenge_path_id')->nullable();
            $table->bigInteger('resource_module_id')->nullable();
            $table->bigInteger('resource_collection_id')->nullable();
            $table->bigInteger('resource_group_id')->nullable();
            $table->integer('sequence')->nullable();
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
        Schema::dropIfExists('component_associations');
    }
};
