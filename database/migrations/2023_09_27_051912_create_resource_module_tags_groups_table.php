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
        Schema::create('resource_module_tags_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_module_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1'])->comment('0->tag, 1-> groups');
            $table->foreign('resource_module_id')->references('id')->on('resource_modules')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_module_tags_groups');
    }
};
