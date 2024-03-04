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
        Schema::create('featured_module', function (Blueprint $table) {
            $table->id();
            $table->enum('module_type', ['0', '1', '2'])->comment('0->labs,1->project,2->challenge');
            $table->unsignedBigInteger('module_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_module');
    }
};
