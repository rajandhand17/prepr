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
        Schema::create('resource_group_type_modes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_group_id');
            $table->enum('type_mode', ['0', '1'])->comment('0 -> type,1 -> mode');
            $table->enum('value', ['0', '1', '2', '3', '4', '5'])->comment('value of type or mode:- 0 -> assess,1 -> onboard, 2 -> engage, 3 -> grow, 4 -> team, 5 -> individual');
            $table->foreign('resource_group_id')->references('id')->on('resource_groups')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_group_type_modes');
    }
};
