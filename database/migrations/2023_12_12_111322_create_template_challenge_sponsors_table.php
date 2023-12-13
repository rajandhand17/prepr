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
        Schema::create('template_challenge_sponsors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_challenge_id');
            $table->unsignedBigInteger('host_id');
            $table->foreign('template_challenge_id')->references('id')->on('template_challenges')->onDelete('cascade');
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_challenge_sponsors');
    }
};
