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
        Schema::create('lab_marketplace_program_tags_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_marketplace_program_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1'])->comment('0->tag, 1-> groups');
            $table->foreign('lab_marketplace_program_id', 'lmp_tags_groups_lab_marketplace_program_id_foreign')->references('id')->on('lab_marketplace_program')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_lab_programs_tags_groups');
    }
};
