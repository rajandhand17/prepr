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
        Schema::create('scorm', function (Blueprint $table) {
            $table->id();
            $table->text('uuid');
            $table->string('resource_type');
            $table->bigInteger('resource_id');
            $table->text('title');
            $table->text('origin_file')->nullable();
            $table->string('version')->nullable();
            $table->double('ratio')->nullable();
            $table->text('identifier')->nullable();
            $table->text('entry_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scorm');
    }
};
