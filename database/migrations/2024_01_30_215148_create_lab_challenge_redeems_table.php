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
        Schema::create('lab_challenge_redeems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->bigInteger('lab_id')->nullable();
            $table->bigInteger('lab_marketplace_id')->nullable();
            $table->bigInteger('challenge_id')->nullable();
            $table->bigInteger('challenge_template_id')->nullable();
            $table->enum('is_redeemed', ['0', '1'])->default('0')->comment("'0' => Module Cloned Ex. Create Lab template from Lab,'1' => Template clone Ex. Create Lab from lab template");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_challenge_redeems');
    }
};
