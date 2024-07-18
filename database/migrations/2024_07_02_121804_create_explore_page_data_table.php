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
        Schema::create('explore_page_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comp_id');
            $table->string('comp_type');
            $table->string('title')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('action_button')->nullable();
            $table->string('role')->nullable();
            $table->string('media_type')->default('image')->nullable();
            $table->text('media')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
