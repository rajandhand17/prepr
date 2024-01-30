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
        Schema::create('challenge_path_template_tags_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_path_template_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1'])->comment('0->tag, 1-> groups');
            $table->foreign('challenge_path_template_id', 'fk_challenge_path_template_tags_groups')->references('id')->on('challenge_path_templates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_path_template_tags_groups');
    }
};
