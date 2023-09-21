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
        Schema::create('resource_module_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_module_id');
            $table->string('title');
            $table->enum('type', ['0', '1', '2', '3', '4', '5', '6', '7', '8'])->comment('0->header,1->document,2->video,3->audio,4->embedded,5->embedded_audio,6->url,7->image,8->Embedded_Cover_Video')->nullable();
            $table->string('path')->nullable();
            $table->string('social_link_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('resource_module_id')->references('id')->on('resource_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_module_details');
    }
};
