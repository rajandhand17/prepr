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
        Schema::create('component_associations', function (Blueprint $table) {
            $table->id();
            $table->integer("lab_id");
            $table->integer("lab_program_id");
            $table->integer("challenge_id");
            $table->integer("challenge_path_id");
            $table->integer("resource_module_id");
            $table->integer("resource_collection_id");
            $table->integer("resource_group_id");
            $table->integer("sequence");
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->timestamps();
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
