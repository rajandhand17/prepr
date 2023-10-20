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
        Schema::create('resource_collection_skills_groups_stacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_collection_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1', '2'])->comment('0->skills, 1->group, 2->stack');
            $table->timestamps();
            $table->softDeletes();
            // Define foreign key constraint with a specific name
            $table->foreign('resource_collection_id')
                ->references('id')
                ->on('resource_collections')
                ->onDelete('cascade')
                ->name('rcsgs_resource_collection_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_collection_skills_groups_stacks');
    }
};
