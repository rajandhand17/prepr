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
        Schema::create('challenge_template_sponsors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_template_id');
            $table->unsignedBigInteger('host_id');
            $table->foreign('challenge_template_id', 'fk_challenge_template_sponsors')->references('id')->on('challenge_templates')->onDelete('cascade');
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
        Schema::dropIfExists('challenge_template_sponsors');
    }
};
