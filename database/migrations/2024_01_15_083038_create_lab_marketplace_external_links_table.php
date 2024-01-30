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
        Schema::create('lab_marketplace_external_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_marketplace_id');
            $table->string('social_media_link');
            $table->unsignedBigInteger('social_link_id');
            $table->foreign('lab_marketplace_id')->references('id')->on('lab_marketplace')->onDelete('cascade');
            $table->foreign('social_link_id')->references('id')->on('social_links')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_marketplace_external_links');
    }
};
