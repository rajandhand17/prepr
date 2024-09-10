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
        Schema::create('challenge_path_templates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('duration_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->string('media_type')->default('image')->nullable();
            $table->string('media')->nullable();
            $table->enum('privacy', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0-> draft, 1-> published, 2-> archive');
            $table->enum('is_achievement_enabled', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_sequential', ['0', '1'])->comment('0-> no,1-> yes')->default('0');
            $table->enum('is_auto_created', ['0', '1'])->default('0')->comment('0->no,1->yes');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('duration_id')->references('id')->on('durations')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_path_templates');
    }
};
