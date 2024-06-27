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
        Schema::create('resource_module_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_id');
            $table->bigInteger('module_asset_id')->comment('Resource module assets ids includes -> Manual uploads, Scorm and go1');
            $table->enum('asset_type', ['0', '1', '2', '3', '4', '5', '6', '7', '8'])->comment('0->doc, 1->video, 2->audio, 3->embedded_video, 4->embedded_audio, 5->url, 6->image, 7->scorm, 8->go1');
            $table->foreign('module_id')->references('id')->on('resource_modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_module_visits');
    }
};
