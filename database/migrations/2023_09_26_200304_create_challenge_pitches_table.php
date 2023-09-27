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
        Schema::create('challenge_pitches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('title')->comment('English pitch question');
            $table->string('fr_CA_title')->comment('French pitch question');
            $table->longText('description')->comment('English pitch description')->nullable();
            $table->longText('fr_CA_description')->comment('French pitch description')->nullable();
            $table->foreign('template_id')->references('id')->on('pitch_templates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_pitches');
    }
};
