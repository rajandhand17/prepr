<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('template_component_associations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('template_lab_id')->nullable();
            $table->bigInteger('template_lab_program_id')->nullable();
            $table->bigInteger('template_challenge_id')->nullable();
            $table->bigInteger('template_challenge_path_id')->nullable();
            $table->bigInteger('template_resource_module_id')->nullable();
            $table->bigInteger('template_resource_collection_id')->nullable();
            $table->bigInteger('template_resource_group_id')->nullable();
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
        Schema::dropIfExists('template_component_associations');
    }
};
