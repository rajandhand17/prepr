<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lab_marketplace_component_associations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lab_marketplace_id')->nullable();
            $table->bigInteger('lab_program_id')->nullable();
            $table->bigInteger('challenge_template_id')->nullable();
            $table->bigInteger('challenge_path_template_id')->nullable();
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
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_marketplace_component_associations');
    }
};
