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
        Schema::create('lab_programs', function (Blueprint $table) {
            $table->id();
            $table->string('language')->default('en');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('lab_id');
            $table->unsignedBigInteger('user_id');
            $table->string('media')->nullable();
            $table->enum('privacy',['0','1'])->default('0')->comment('0->yes,1->no')->nullable();
            $table->enum('status',['0','1','2'])->default('0')->comment('0-> draft, 1-> published, 2-> archive')->nullable();
            $table->enum('is_auto_created',['0','1'])->default('0')->comment('0->yes,1->no')->nullable();
            $table->string('prize')->nullable();
            $table->string('points')->nullable();
            $table->string('trophy')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_programs');
    }
};
