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
        Schema::create('resource_groups', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->enum('media_type', ['0', '1'])->default('0')->comment('0 -> Image, 1-> embedded, Type of media defining of media');
            $table->text('media')->nullable();
            $table->unsignedBigInteger('level');
            $table->unsignedBigInteger('duration');
            $table->enum('privacy', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0-> draft, 1-> published, 2-> archive');
            $table->enum('is_auto_created', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('level')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('duration')->references('id')->on('durations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_groups');
    }
};
