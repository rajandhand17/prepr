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
        Schema::create('resource_module_skills_groups_stacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_module_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1', '2'])->comment('0->skills, 1->group,2->stack');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('resource_module_id')->references('id')->on('resource_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_module_skills_groups_stacks');
    }
};
