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
        Schema::create('resource_collections', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('media_type')->default('image')->nullable();
            $table->string('media')->nullable();
            $table->integer('level');
            $table->integer('duration');
            $table->enum('privacy', ['0', '1'])->default('0')->comment('0->no,1->yes')->nullable();
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0-> draft, 1-> published, 2-> archive')->nullable();
            $table->enum('is_accessible', ['0', '1'])->comment('0-> no, 1-> yes')->default('1');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_collections');
    }
};
