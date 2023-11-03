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
        Schema::create('resource_group_tags_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_group_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1'])->comment('0->tag, 1-> groups');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('resource_group_id')
                ->references('id')
                ->on('resource_groups')
                ->onDelete('cascade')
                ->name('rgtg_resource_group_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_group_tags_groups');
    }
};
