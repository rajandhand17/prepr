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
        Schema::create('organization_external_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('social_media_link');
            $table->unsignedBigInteger('social_link_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('social_link_id')->references('id')->on('social_links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_external_links');
    }
};
