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
        Schema::create('challenge_resource_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('challenge_id')->unsigned();
            $table->bigInteger('resource_module_id')->unsigned();
            $table->timestamps();

            // Foreign key constraint for 'challenge_id' referencing 'id' in 'challenges'
            $table->foreign('challenge_id')
                ->references('id')->on('challenges')
                ->onDelete('cascade');

            // Foreign key constraint for 'resource_module_id' referencing 'id' in 'resource_modules'
            $table->foreign('resource_module_id')
                ->references('id')->on('resource_modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_resource_modules');
    }
};
