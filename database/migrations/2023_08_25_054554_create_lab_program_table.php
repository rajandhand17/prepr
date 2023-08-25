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
        Schema::create('lab_program', function (Blueprint $table) {
            $table->id();
            $table->string('language')->default('en');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('user_id');
            $table->string('media')->nullable();
            $table->enum('privacy',['0','1'])->default('0')->comment('0->public','1->private')->nullable();
            $table->enum('status',['0','1'])->default('0')->comment('0->public','1->private')->nullable();
            $table->unsignedTinyInteger('is_auto_create')->nullable();
            $table->string('prize')->nullable();
            $table->string('points')->nullable();
            $table->string('trophy')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_program');
    }
};
